<?php

$playsJson = '{"hamlet": {"name": "Hamlet", "type": "tragedy"},"as-like": {"name": "As You Like It", "type": "comedy"},"othello": {"name": "Othello", "type": "tragedy"}}';
$invoiceJson = '[{"customer": "BigCo","performances": [{"playID": "hamlet","audience": 55},{"playID": "as-like","audience": 35},{"playID": "othello","audience": 40}]}]';

$plays = json_decode($playsJson,true);
$invoice = json_decode($invoiceJson);


function statement ($invoice, $plays)
{


//    let totalAmount = 0;
//    let volumeCredits = 0;
    $totalAmount = 0;
    $volumeCredits = 0;
    $play = new stdClass();

//    let result = `Statement for ${invoice.customer}\n`;
    $result = "Statement for {$invoice[0]->customer}\n";

//    const $format = new Intl.NumberFormat("en-US",
//                          { style: "currency", currency: "USD",
//                            minimumFractionDigits: 2 }).format;

    $format = "number_format";

//    for (let perf of invoice.performances) {
    foreach ($invoice[0]->performances as $perf) {
//    const play = plays[perf.playID];
        $play = $plays[$perf->playID];
//        let thisAmount = 0;
        $thisAmount = 0;

//      switch (play.type) {
//          case "tragedy":
//              thisAmount = 40000;
//              if (perf.audience > 30) {
//                  thisAmount += 1000 * (perf.audience - 30);
//              }
//              break;
//          case "comedy":
//              thisAmount = 30000;
//              if (perf.audience > 20) {
//                  thisAmount += 10000 + 500 * (perf.audience - 20);
//              }
//              thisAmount += 300 * perf.audience;
//              break;
//          default:
//              throw new Error(`unknown type: ${play.type}`);
//
//      }
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
                throw new Error(`unknown type: {$play["type"]}`);

        }

        // add volume credits
//      volumeCredits += Math.max(perf.audience - 30, 0);
        $volumeCredits += max($perf->audience - 30, 0);

        // add extra credit for every ten comedy attendees
//      if ("comedy" === play.type) volumeCredits += Math.floor(perf.audience / 5);
        if ("comedy" === $play["type"]) $volumeCredits += floor($perf->audience / 5);

        // print line for this order
//      result += `  ${play.name}: ${format(thisAmount/100)} (${perf.audience} seats)\n`;
        $result .= "  {$play["name"]}: {$format($thisAmount/100,2)} ({$perf->audience} seats)\n";

//      totalAmount += thisAmount;
        $totalAmount += $thisAmount;
    }
//    result += `Amount owed is ${format(totalAmount/100)}\n`;
    $result .= "Amount owed is {$format($totalAmount/100,2)}\n";

//    result += `You earned ${volumeCredits} credits\n`;
    $result .= "You earned {$volumeCredits} credits\n";

//    return result;
    return $result;


}

echo statement($invoice,$plays);
