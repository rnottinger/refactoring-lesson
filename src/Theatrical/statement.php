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
 */
function statement ($invoice, $plays) : string
{
    $statementData = new \stdClass();
    $statementData->customer = $invoice[0]->customer;
    return renderPlainText($statementData, $invoice, $plays);
}

function renderPlainText($data, $invoice, $plays): string
{

//    $result = "Statement for {$invoice[0]->customer}\n";
    $result = "Statement for {$data->customer}\n";

    foreach ($invoice[0]->performances as $perf) {

        try {
            // print line for this order
            $result .= "  " . playFor($plays, $perf)["name"] . ": " . usd(amountFor($plays, $perf)) . " ({$perf->audience} seats)\n";
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    $result .= "Amount owed is " . usd(totalAmount($invoice, $plays)) . "\n";
    $result .= "You earned " . totalVolumeCredits($invoice, $plays) . " credits\n";
    return $result;
}
function totalAmount($invoice, $plays): int
{
    $result = 0;
    foreach ($invoice[0]->performances as $aPerformance) {
        $result += amountFor($plays, $aPerformance);
    }
    return $result;
}
function totalVolumeCredits($invoice, $plays): int
{
    $result = 0;
    foreach ($invoice[0]->performances as $aPerformance) {
        $result += volumeCreditsFor($plays, $aPerformance);
    }
    return $result;
}
function usd($aNumber): string
{
    return number_format($aNumber/100,2);
}
function volumeCreditsFor($plays, $aPerformance)
{
    $result = 0;
    // add volume credits
    $result += max($aPerformance->audience - 30, 0);

    // add extra credit for every ten comedy attendees
    if ("comedy" === playFor($plays, $aPerformance)["type"]) $result += floor($aPerformance->audience / 5);

    return $result;
}
function playFor($plays, $aPerformance) {
    return $plays[$aPerformance->playID];
}
function amountFor($plays, $aPerformance): int
{
    $result = 0;
    switch (playFor($plays, $aPerformance)["type"]) {
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
            throw new \Exception("unknown play type: " . playFor($plays, $aPerformance)["type"]);
    }
    return $result;
}
