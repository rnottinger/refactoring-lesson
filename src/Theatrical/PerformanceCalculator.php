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
    }

    /**
     * @return mixed
     */
    public function volumeCredits()
    {
        return max($this->performance->audience - 30, 0);
    }
}