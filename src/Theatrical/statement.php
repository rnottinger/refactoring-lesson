<?php

namespace App\Theatrical;

use stdClass;

function statement ($invoice, $plays) : string
{
    $totalAmount = 0;
    $volumeCredits = 0;
    $play = new stdClass();

    $result = "Statement for {$invoice[0]->customer}\n";
    $format = "number_format";
    foreach ($invoice[0]->performances as $perf) {
        $play = $plays[$perf->playID];
        $thisAmount = 0;
        try {
            switch ($play["type"]) {
                case "tragedy":
                    $thisAmount = 40000;
                    if ($perf->audience > 30) {
                        $thisAmount += 1000 * ($perf->audience - 30);
                    }
                    break;
                case "comedy":
                    $thisAmount = 30000;
                    if ($perf->audience > 20) {
                        $thisAmount += 10000 + 500 * ($perf->audience - 20);
                    }
                    $thisAmount += 300 * $perf->audience;
                    break;
                default:
                    throw new \Exception("unknown play type: {$play["type"]}");
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        // add volume credits
        $volumeCredits += max($perf->audience - 30, 0);

        // add extra credit for every ten comedy attendees
        if ("comedy" === $play["type"]) $volumeCredits += floor($perf->audience / 5);

        // print line for this order
        $result .= "  {$play["name"]}: {$format($thisAmount/100,2)} ({$perf->audience} seats)\n";
        $totalAmount += $thisAmount;
    }
    $result .= "Amount owed is {$format($totalAmount/100,2)}\n";
    $result .= "You earned {$volumeCredits} credits\n";
    return $result;
}
