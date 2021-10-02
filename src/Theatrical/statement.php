<?php

namespace App\Theatrical;


function statement ($invoice, $plays) : string
{
//    $totalAmount = 0;

    $result = "Statement for {$invoice[0]->customer}\n";

    foreach ($invoice[0]->performances as $perf) {

        try {
            // print line for this order
            $result .= "  " . playFor($plays, $perf)["name"] . ": " . usd(amountFor($plays, $perf)) . " ({$perf->audience} seats)\n";
//            $totalAmount += amountFor($plays, $perf);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

//    $totalAmount = appleSauce($plays, $invoice);

//    $result .= "Amount owed is " . usd($totalAmount) . "\n";
    $result .= "Amount owed is " . usd(appleSauce($plays, $invoice)) . "\n";
    $result .= "You earned " . totalVolumeCredits($plays, $invoice) . " credits\n";
    return $result;
}

function appleSauce($plays, $invoice): int
{
    $totalAmount = 0;
    foreach ($invoice[0]->performances as $aPerformance) {
        $totalAmount += amountFor($plays, $aPerformance);
    }
    return $totalAmount;

}

function totalVolumeCredits($plays, $invoice)
{
    $volumeCredits = 0;
    foreach ($invoice[0]->performances as $aPerformance) {
        $volumeCredits += volumeCreditsFor($plays, $aPerformance);
    }
    return $volumeCredits;
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
