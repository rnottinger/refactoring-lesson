<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

use App\Theatrical;

include __DIR__ . '/../../src/Theatrical/statement.php';

class StatementTest extends TestCase
{
    /** @test */
    public function it_generates_a_plain_text_statement()
    {
        $playsJson = '{"hamlet": {"name": "Hamlet", "type": "tragedy"},"as-like": {"name": "As You Like It", "type": "comedy"},"othello": {"name": "Othello", "type": "tragedy"}}';
        $invoiceJson = '[{"customer": "BigCo","performances": [{"playID": "hamlet","audience": 55},{"playID": "as-like","audience": 35},{"playID": "othello","audience": 40}]}]';

        $plays = json_decode($playsJson,true);
        $invoice = json_decode($invoiceJson);

        $expected = "Statement for BigCo\n  Hamlet: 650.00 (55 seats)\n  As You Like It: 580.00 (35 seats)\n  Othello: 500.00 (40 seats)\nAmount owed is 1,730.00\nYou earned 47 credits\n";

        $actual = Theatrical\statement($invoice, $plays);

        $this->assertSame($expected,$actual,'The statement printed does not match the expected statement');

    }


    /** @test */
    public function it_generates_a_html_statement()
    {
        $playsJson = '{"hamlet": {"name": "Hamlet", "type": "tragedy"},"as-like": {"name": "As You Like It", "type": "comedy"},"othello": {"name": "Othello", "type": "tragedy"}}';
        $invoiceJson = '[{"customer": "BigCo","performances": [{"playID": "hamlet","audience": 55},{"playID": "as-like","audience": 35},{"playID": "othello","audience": 40}]}]';

        $plays = json_decode($playsJson,true);
        $invoice = json_decode($invoiceJson);

        $expected = "<h1>Statement for BigCo</h1>\n<table>\n<tr><th>play</th><th>seats</th><th>cost</th></tr>  <tr><td>Hamlet</td><td>55</td><td>650.00</td></tr>\n  <tr><td>As You Like It</td><td>35</td><td>580.00</td></tr>\n  <tr><td>Othello</td><td>40</td><td>500.00</td></tr>\n</table>\n<p>Amount owed is <em>1,730.00</em></p>\n<p>You earned <em>47</em> credits</p>\n";

        $actual = Theatrical\htmlStatement($invoice, $plays);

        $this->assertSame($expected,$actual,'The statement printed does not match the expected statement');

    }

    /** @test */
    public function the_play_type_is_unknown()
    {
        $unknownPlayType = 'coffee';

        $playsJson = '{"hamlet": {"name": "Hamlet", "type": "' . $unknownPlayType .'"},"as-like": {"name": "As You Like It", "type": "comedy"},"othello": {"name": "Othello", "type": "tragedy"}}';
        $invoiceJson = '[{"customer": "BigCo","performances": [{"playID": "hamlet","audience": 55},{"playID": "as-like","audience": 35},{"playID": "othello","audience": 40}]}]';

        $plays = json_decode($playsJson,true);
        $invoice = json_decode($invoiceJson);

        $expected="unknown play type: coffee";

        $actual = Theatrical\statement($invoice, $plays);

        $this->assertSame($expected,$actual,'The error message does not match the expected message');

    }
}


/**
 * - Since the statement `returns a string`,
- what I do is create `a few invoices`,
- give each invoice `a few performances`
- of various `kinds of plays`,
- and generate `the statement strings`.
- I then do `a string comparison`
- between `the new string`
- and `some reference strings`
- that I have hand-checked.
 */
