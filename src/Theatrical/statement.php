<?php

namespace App\Theatrical;

include __DIR__ . '/createStatementData.php';

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
        return renderPlainText(createStatementData($invoice, $plays));
    } catch (\Exception $e) {
        return $e->getMessage();
    }
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

/**
 * @param $invoice
 * @param $plays
 * @return string
 */
function htmlStatement ($invoice, $plays): string
{
    try {
        return renderHtml(createStatementData($invoice, $plays));
    } catch (\Exception $e) {
        return $e->getMessage();
    }
}

function renderHtml ($data): string
{
    $result = "<h1>Statement for {$data->customer}</h1>\n";
    $result .= "<table>\n";
    $result .= "<tr><th>play</th><th>seats</th><th>cost</th></tr>";
    foreach ($data->performances as $perf) {
        $result .= "  <tr><td>" . $perf->play["name"] . "</td><td>{$perf->audience}</td>";
        $result .= "<td>" . usd($perf->amount) . "</td></tr>\n";
    }
    $result .= "</table>\n";
    $result .= "<p>Amount owed is <em>" . usd($data->totalAmount) . "</em></p>\n";
    $result .= "<p>You earned <em>" . $data->totalVolumeCredits . "</em> credits</p>\n";
    return $result;
}


function usd($aNumber): string
{
    return number_format($aNumber/100,2);
}
