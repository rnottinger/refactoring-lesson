<?php

namespace App\Theatrical;

/**
 *
 */
class ComedyCalculator extends PerformanceCalculator
{

    /**
     * @return float|int
     */
    public function amount()
    {
        $result = 30000;
        if ($this->performance->audience > 20) {
            $result += 10000 + 500 * ($this->performance->audience - 20);
        }
        $result += 300 * $this->performance->audience;
        return $result;
    }

    public function volumeCredits()
    {
//        $result = 0;
//        // add volume credits
//        $result += max($this->performance->audience - 30, 0);
//
//        // add extra credit for every ten comedy attendees
//        if ("comedy" === $this->play["type"]) $result += floor($this->performance->audience / 5);
//
//        return $result;
        return parent::volumeCredits() + floor($this->performance->audience / 5);
    } // end volumeCredits

}