<?php

namespace App\Theatrical;

/**
 * The top-level statement function
 * is now just seven lines of code,
 * and all it does is laying out
 * the printing of the statement.
 *
 * @param $invoice
 * @param $plays
 * @return string
 * @throws \Exception
 */
function statement ($invoice, $plays) : string
{

    try {
//        $statementData = new \stdClass();
//        $statementData->customer = $invoice[0]->customer;
//        $statementData->performances = array_map(static function ($aPerformance) use ($plays) {
//            $result = clone $aPerformance;
//            $result->play = playFor($plays, $result);
//            $result->amount = amountFor($result);
//            $result->volumeCredits = volumeCreditsFor($result);
//            return $result;
//        }, $invoice[0]->performances);
//        $statementData->totalAmount = totalAmount($statementData);
//        $statementData->totalVolumeCredits = totalVolumeCredits($statementData);

//        return renderPlainText($statementData);
        return renderPlainText(createStatementData($invoice, $plays));
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

function createStatementData($invoice, $plays): \stdClass
{
    $statementData = new \stdClass();
    $statementData->customer = $invoice[0]->customer;
    $statementData->performances = array_map(static function ($aPerformance) use ($plays) {
        $result = clone $aPerformance;
        $result->play = playFor($plays, $result);
        $result->amount = amountFor($result);
        $result->volumeCredits = volumeCreditsFor($result);
        return $result;
    }, $invoice[0]->performances);
    $statementData->totalAmount = totalAmount($statementData);
    $statementData->totalVolumeCredits = totalVolumeCredits($statementData);
    return $statementData;
}

function renderPlainText($data): string
{
    $result = "Statement for {$data->customer}\n";

    foreach ($data->performances as $perf) {
            $result .= "  " . $perf->play["name"] . ": " . usd($perf->amount) . " ({$perf->audience} seats)\n";
    }

    $result .= "Amount owed is " . usd($data->totalAmount) . "\n";

    $result .= "You earned " . $data->totalVolumeCredits . " credits\n";
    return $result;
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

function usd($aNumber): string
{
    return number_format($aNumber/100,2);
}

function volumeCreditsFor($aPerformance)
{
    $result = 0;
    // add volume credits
    $result += max($aPerformance->audience - 30, 0);

    // add extra credit for every ten comedy attendees
    if ("comedy" === $aPerformance->play["type"]) $result += floor($aPerformance->audience / 5);

    return $result;
}

function playFor($plays, $aPerformance) {
    return $plays[$aPerformance->playID];
}

function amountFor($aPerformance): int
{
    $result = 0;
    switch ($aPerformance->play["type"]) {
        case "tragedy":
            $result = 40000;
            if ($aPerformance->audience > 30) {
                $result += 1000 * ($aPerformance->audience - 30);
            }
            break;
        case "comedy":
            $result = 30000;
            if ($aPerformance->audience > 20) {
                $result += 10000 + 500 * ($aPerformance->audience - 20);
            }
            $result += 300 * $aPerformance->audience;
            break;
        default:
            throw new \Exception("unknown play type: " . $aPerformance->play["type"]);
    }
    return $result;
}
