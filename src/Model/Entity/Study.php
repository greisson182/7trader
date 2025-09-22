<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Study extends Entity
{
    protected $_accessible = [
        'student_id' => true,
        'market_date' => true,
        'study_date' => true,
        'wins' => true,
        'losses' => true,
        'profit_loss' => true,
        'created' => true,
        'modified' => true,
        'student' => true,
    ];

    protected $_hidden = [
        'id',
    ];

    protected $_virtual = [
        'total_trades',
        'win_rate'
    ];

    /**
     * Get total number of trades
     */
    protected function _getTotalTrades(): int
    {
        return $this->wins + $this->losses;
    }

    /**
     * Calculate win rate percentage
     */
    protected function _getWinRate(): float
    {
        $totalTrades = $this->total_trades;
        return $totalTrades > 0 ? round(($this->wins / $totalTrades) * 100, 2) : 0;
    }
}