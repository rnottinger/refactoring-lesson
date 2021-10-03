<?php

namespace App\Theatrical;

/**
 *
 */
class PerformanceCalculator
{
    public $performance;
    public $play;

    /**
     * @param $aPerformance
     * @param $aPlay
     */
    public function __construct($aPerformance, $aPlay) {
        $this->performance = $aPerformance;
        $this->play = $aPlay;
    }

    /**
     * @throws \Exception
     */
    public function amount()
    {
        throw new \Exception('subclass responsibility');
    } // end amount

    /**
     * @return false|float|int|mixed
     */
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