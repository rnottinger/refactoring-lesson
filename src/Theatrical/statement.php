<?php

namespace App\Theatrical;


function statement ($invoice, $plays) : string
{
    $totalAmount = 0;
    $volumeCredits = 0;

    $result = "Statement for {$invoice[0]->customer}\n";
    $format = "number_format";
    foreach ($invoice[0]->performances as $perf) {

        // add volume credits
        $volumeCredits += max($perf->audience - 30, 0);

        // add extra credit for every ten comedy attendees
        if ("comedy" === playFor($plays, $perf)["type"]) $volumeCredits += floor($perf->audience / 5);

        try {
            // print line for this order
            $result .= "  " . playFor($plays, $perf)["name"] . ": " . $format(amountFor($plays, $perf)/100,2) . " ({$perf->audience} seats)\n";
            $totalAmount += amountFor($plays, $perf);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
    $result .= "Amount owed is {$format($totalAmount/100,2)}\n";
    $result .= "You earned {$volumeCredits} credits\n";
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
