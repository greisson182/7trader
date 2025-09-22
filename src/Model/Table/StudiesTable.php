<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class StudiesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('studies');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('student_id')
            ->requirePresence('student_id', 'create')
            ->notEmptyString('student_id');

        $validator
            ->date('market_date')
            ->requirePresence('market_date', 'create')
            ->notEmptyDate('market_date');

        $validator
            ->date('study_date')
            ->requirePresence('study_date', 'create')
            ->notEmptyDate('study_date');

        $validator
            ->integer('wins')
            ->greaterThanOrEqual('wins', 0)
            ->requirePresence('wins', 'create')
            ->notEmptyString('wins');

        $validator
            ->integer('losses')
            ->greaterThanOrEqual('losses', 0)
            ->requirePresence('losses', 'create')
            ->notEmptyString('losses');

        $validator
            ->decimal('profit_loss')
            ->requirePresence('profit_loss', 'create')
            ->notEmptyString('profit_loss');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('student_id', 'Students'), ['errorField' => 'student_id']);

        return $rules;
    }

    /**
     * Calculate win rate for a study
     */
    public function calculateWinRate(int $wins, int $losses): float
    {
        $totalTrades = $wins + $losses;
        return $totalTrades > 0 ? ($wins / $totalTrades) * 100 : 0;
    }

    /**
     * Get studies by date range
     */
    public function findByDateRange(Query $query, array $options): Query
    {
        $startDate = $options['start_date'] ?? null;
        $endDate = $options['end_date'] ?? null;

        if ($startDate) {
            $query->where(['study_date >=' => $startDate]);
        }

        if ($endDate) {
            $query->where(['study_date <=' => $endDate]);
        }

        return $query;
    }

    /**
     * Get studies for a specific student
     */
    public function findByStudent(Query $query, array $options): Query
    {
        $studentId = $options['student_id'] ?? null;

        if ($studentId) {
            $query->where(['student_id' => $studentId]);
        }

        return $query;
    }
}