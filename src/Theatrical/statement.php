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
    $statementData = new \stdClass();
    $statementData->customer = $invoice[0]->customer;

    try {
        $statementData->performances = array_map(static function ($aPerformance) use ($plays) {
            $result = clone $aPerformance;
            $result->play = playFor($plays, $result);
            $result->amount = amountFor($result);
            return $result;
        }, $invoice[0]->performances);

//    return renderPlainText($statementData, $plays);
        return renderPlainText($statementData);
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

//function renderPlainText($data, $plays): string
function renderPlainText($data): string
{
    $result = "Statement for {$data->customer}\n";

    foreach ($data->performances as $perf) {
//
//        try {
            // print line for this order
//            $result .= "  " . playFor($plays, $perf)["name"] . ": " . usd(amountFor($plays, $perf)) . " ({$perf->audience} seats)\n";
//            $result .= "  " . $perf->play["name"] . ": " . usd(amountFor($plays, $perf)) . " ({$perf->audience} seats)\n";
            $result .= "  " . $perf->play["name"] . ": " . usd($perf->amount) . " ({$perf->audience} seats)\n";
//        } catch (\Exception $e) {
//            return $e->getMessage();
//        }
    }

//    $result .= "Amount owed is " . usd(totalAmount($data->performances, $plays)) . "\n";
    $result .= "Amount owed is " . usd(totalAmount($data->performances)) . "\n";

//    $result .= "You earned " . totalVolumeCredits($data->performances, $plays) . " credits\n";
    $result .= "You earned " . totalVolumeCredits($data->performances) . " credits\n";
    return $result;
}

//function totalAmount($performances, $plays): int
function totalAmount($performances): int
{
    $result = 0;
    foreach ($performances as $aPerformance) {
//        $result += amountFor($plays, $aPerformance);
        $result += $aPerformance->amount;
    }
    return $result;
}

//function totalVolumeCredits($performances, $plays): int
function totalVolumeCredits($performances): int
{
    $result = 0;
    foreach ($performances as $aPerformance) {
//        $result += volumeCreditsFor($plays, $aPerformance);
        $result += volumeCreditsFor($aPerformance);
    }
    return $result;
}

function usd($aNumber): string
{
    return number_format($aNumber/100,2);
}

//function volumeCreditsFor($plays, $aPerformance)
function volumeCreditsFor($aPerformance)
{
    $result = 0;
    // add volume credits
    $result += max($aPerformance->audience - 30, 0);

    // add extra credit for every ten comedy attendees
//    if ("comedy" === playFor($plays, $aPerformance)["type"]) $result += floor($aPerformance->audience / 5);
    if ("comedy" === $aPerformance->play["type"]) $result += floor($aPerformance->audience / 5);

    return $result;
}

function playFor($plays, $aPerformance) {
    return $plays[$aPerformance->playID];
}

//function amountFor($plays, $aPerformance): int
function amountFor($aPerformance): int
{
    $result = 0;
//    switch (playFor($plays, $aPerformance)["type"]) {
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
//            throw new \Exception("unknown play type: " . playFor($plays, $aPerformance)["type"]);
            throw new \Exception("unknown play type: " . $aPerformance->play["type"]);
    }
    return $result;
}
