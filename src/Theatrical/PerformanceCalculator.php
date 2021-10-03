<?php

namespace App\Theatrical;


class PerformanceCalculator
{
    public $performance;
    public $play;

    public function __construct($aPerformance, $aPlay) {
        $this->performance = $aPerformance;
        $this->play = $aPlay;
    }

    public function amount()
    {
        $result = 0;
//    switch ($aPerformance->play["type"]) {
        switch ($this->play["type"]) {
            case "tragedy":
                $result = 40000;
//                if ($aPerformance->audience > 30) {
                if ($this->performance->audience > 30) {
//                    $result += 1000 * ($aPerformance->audience - 30);
                    $result += 1000 * ($this->performance->audience - 30);
                }
                break;
            case "comedy":
                $result = 30000;
//                if ($aPerformance->audience > 20) {
                if ($this->performance->audience > 20) {
//                    $result += 10000 + 500 * ($aPerformance->audience - 20);
                    $result += 10000 + 500 * ($this->performance->audience - 20);
                }
//                $result += 300 * $aPerformance->audience;
                $result += 300 * $this->performance->audience;
                break;
            default:
//                throw new \Exception("unknown play type: " . $aPerformance->play["type"]);
                throw new \Exception("unknown play type: " . $this->play["type"]);
        }
        return $result;

    } // end amount

    public function volumeCredits()
    {
        $result = 0;
        // add volume credits
        $result += max($this->performance->audience - 30, 0);

        // add extra credit for every ten comedy attendees
        if ("comedy" === $this->play["type"]) $result += floor($this->performance->audience / 5);

        return $result;
    } // end volumeCredits
} // end PerformanceCalculator