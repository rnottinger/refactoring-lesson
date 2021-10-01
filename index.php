<?php

use App\Theatrical;
include 'src/Theatrical/statement.php';

$playsJson = '{"hamlet": {"name": "Hamlet", "type": "tragedy"},"as-like": {"name": "As You Like It", "type": "comedy"},"othello": {"name": "Othello", "type": "tragedy"}}';
$invoiceJson = '[{"customer": "BigCo","performances": [{"playID": "hamlet","audience": 55},{"playID": "as-like","audience": 35},{"playID": "othello","audience": 40}]}]';

$plays = json_decode($playsJson, true);
$invoice = json_decode($invoiceJson);


echo Theatrical\statement($invoice, $plays);
