<?php

namespace App\Theatrical;

include __DIR__ . '/PerformanceCalculator.php';

/**
 * @param $invoice
 * @param $plays
 * @return \stdClass
 * @throws \Exception
 */
function createStatementData($invoice, $plays): \stdClass
{
    $data = new \stdClass();
    $data->customer = $invoice[0]->customer;
    $data->performances = array_map(static function ($aPerformance) use ($plays) {
        $calculator = createPerformanceCalculator($aPerformance, playFor($plays, $aPerformance));
        $result = clone $aPerformance;
        $result->play = $calculator->play;
        $result->amount = $calculator->amount();
        $result->volumeCredits = $calculator->volumeCredits();
        return $result;
    }, $invoice[0]->performances);
    $data->totalAmount = totalAmount($data);
    $data->totalVolumeCredits = totalVolumeCredits($data);
    return $data;
}

/**
 * @param $aPerformance
 * @param $aPlay
 * @return ComedyCalculator|TragedyCalculator
 * @throws \Exception
 */
function createPerformanceCalculator($aPerformance, $aPlay)
{
    switch($aPlay["type"]) {
        case "tragedy": return new TragedyCalculator($aPerformance, $aPlay);
        case "comedy" : return new ComedyCalculator($aPerformance, $aPlay);
        default:
            throw new \Exception("unknown play type: {$aPlay["type"]}");
    }
}

/**
 * @param $plays
 * @param $aPerformance
 * @return mixed
 */
function playFor($plays, $aPerformance) {
    return $plays[$aPerformance->playID];
}

/**
 * @param $plays
 * @param $aPerformance
 * @return int
 * @throws \Exception
 */
function amountFor($plays, $aPerformance): int
{
    $calc = new PerformanceCalculator(
        $aPerformance,
        playFor($plays, $aPerformance)
    );
    return $calc->amount();

}

/**
 * @param $data
 * @return int
 */
function totalAmount($data): int
{
    return array_reduce($data->performances, static function ($total, $p) {
        $total += $p->amount;
        return $total;
    }, 0);
}

/**
 * @param $data
 * @return int
 */
function totalVolumeCredits($data): int
{
    return array_reduce($data->performances, static function ($total, $p) {
        $total += $p->volumeCredits;
        return $total;
    }, 0);
}