<?php

namespace App\Theatrical;

include __DIR__ . '/PerformanceCalculator.php';

function createStatementData($invoice, $plays): \stdClass
{
    $data = new \stdClass();
    $data->customer = $invoice[0]->customer;
    $data->performances = array_map(static function ($aPerformance) use ($plays) {
        $calculator = new PerformanceCalculator($aPerformance, playFor($plays, $aPerformance));
        $result = clone $aPerformance;
//        $result->play = playFor($plays, $result);
        $result->play = $calculator->play;
//        $result->amount = amountFor($plays, $result);
        $result->amount = $calculator->amount();
//        $result->volumeCredits = volumeCreditsFor($result);
        $result->volumeCredits = $calculator->volumeCredits();
        return $result;
    }, $invoice[0]->performances);
    $data->totalAmount = totalAmount($data);
    $data->totalVolumeCredits = totalVolumeCredits($data);
    return $data;
}

//function volumeCreditsFor($aPerformance)
//{
//    $result = 0;
//    // add volume credits
//    $result += max($aPerformance->audience - 30, 0);
//
//    // add extra credit for every ten comedy attendees
//    if ("comedy" === $aPerformance->play["type"]) $result += floor($aPerformance->audience / 5);
//
//    return $result;
//}

function playFor($plays, $aPerformance) {
    return $plays[$aPerformance->playID];
}

/**
 * @throws \Exception
 */
function amountFor($plays, $aPerformance): int
{   $calc = new PerformanceCalculator(
        $aPerformance,
        playFor($plays, $aPerformance)
    );
    return $calc->amount();



//    $result = 0;
//    switch ($aPerformance->play["type"]) {
//        case "tragedy":
//            $result = 40000;
//            if ($aPerformance->audience > 30) {
//                $result += 1000 * ($aPerformance->audience - 30);
//            }
//            break;
//        case "comedy":
//            $result = 30000;
//            if ($aPerformance->audience > 20) {
//                $result += 10000 + 500 * ($aPerformance->audience - 20);
//            }
//            $result += 300 * $aPerformance->audience;
//            break;
//        default:
//            throw new \Exception("unknown play type: " . $aPerformance->play["type"]);
//    }
//    return $result;
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