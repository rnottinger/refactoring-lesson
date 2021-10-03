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

}