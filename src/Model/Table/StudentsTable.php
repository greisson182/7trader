<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class StudentsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('students');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('Studies', [
            'foreignKey' => 'student_id',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['email']), ['errorField' => 'email']);

        return $rules;
    }

    /**
     * Get daily metrics for a student
     */
    public function getDailyMetrics(int $studentId, string $date): array
    {
        $studies = $this->Studies->find()
            ->where([
                'student_id' => $studentId,
                'study_date' => $date
            ])
            ->toArray();

        $totalWins = array_sum(array_column($studies, 'wins'));
        $totalLosses = array_sum(array_column($studies, 'losses'));
        $totalTrades = $totalWins + $totalLosses;
        $totalProfitLoss = array_sum(array_column($studies, 'profit_loss'));
        
        $winRate = $totalTrades > 0 ? ($totalWins / $totalTrades) * 100 : 0;

        return [
            'date' => $date,
            'total_wins' => $totalWins,
            'total_losses' => $totalLosses,
            'total_trades' => $totalTrades,
            'win_rate' => round($winRate, 2),
            'total_profit_loss' => $totalProfitLoss
        ];
    }

    /**
     * Get monthly metrics for a student
     */
    public function getMonthlyMetrics(int $studentId, int $year, int $month): array
    {
        $startDate = sprintf('%04d-%02d-01', $year, $month);
        $endDate = date('Y-m-t', strtotime($startDate));

        $studies = $this->Studies->find()
            ->where([
                'student_id' => $studentId,
                'study_date >=' => $startDate,
                'study_date <=' => $endDate
            ])
            ->toArray();

        $totalWins = array_sum(array_column($studies, 'wins'));
        $totalLosses = array_sum(array_column($studies, 'losses'));
        $totalTrades = $totalWins + $totalLosses;
        $totalProfitLoss = array_sum(array_column($studies, 'profit_loss'));
        
        $winRate = $totalTrades > 0 ? ($totalWins / $totalTrades) * 100 : 0;

        return [
            'year' => $year,
            'month' => $month,
            'total_wins' => $totalWins,
            'total_losses' => $totalLosses,
            'total_trades' => $totalTrades,
            'win_rate' => round($winRate, 2),
            'total_profit_loss' => $totalProfitLoss
        ];
    }
}