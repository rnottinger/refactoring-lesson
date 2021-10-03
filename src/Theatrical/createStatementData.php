<?php

namespace App\Theatrical;

include __DIR__ . '/PerformanceCalculator.php';

function createStatementData($invoice, $plays): \stdClass
{
    $data = new \stdClass();
    $data->customer = $invoice[0]->customer;
    $data->performances = array_map(static function ($aPerformance) use ($plays) {
//        $calculator = new PerformanceCalculator($aPerformance, playFor($plays, $aPerformance));
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

function createPerformanceCalculator($aPerformance, $aPlay): PerformanceCalculator
{
    return new PerformanceCalculator($aPerformance, $aPlay);
}

function playFor($plays, $aPerformance) {
    return $plays[$aPerformance->playID];
}

/**
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

function totalAmount($data): int
{
    return array_reduce($data->performances, static function ($total, $p) {
        $total += $p->amount;
        return $total;
    }, 0);
}

function totalVolumeCredits($data): int
{
    return array_reduce($data->performances, static function ($total, $p) {
        $total += $p->volumeCredits;
        return $total;
    }, 0);
}