<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CoursesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('courses');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('CourseVideos', [
            'foreignKey' => 'course_id',
            'dependent' => true,
        ]);

        $this->hasMany('CourseEnrollments', [
            'foreignKey' => 'course_id',
            'dependent' => true,
        ]);

        $this->hasMany('StudentProgress', [
            'foreignKey' => 'course_id',
            'dependent' => true,
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('thumbnail')
            ->maxLength('thumbnail', 500)
            ->allowEmptyString('thumbnail');

        $validator
            ->integer('duration_minutes')
            ->allowEmptyString('duration_minutes');

        $validator
            ->scalar('difficulty')
            ->inList('difficulty', ['Iniciante', 'Intermediário', 'Avançado'])
            ->allowEmptyString('difficulty');

        $validator
            ->scalar('category')
            ->maxLength('category', 100)
            ->allowEmptyString('category');

        $validator
            ->scalar('instructor')
            ->maxLength('instructor', 255)
            ->allowEmptyString('instructor');

        $validator
            ->decimal('price')
            ->allowEmptyString('price');

        $validator
            ->boolean('is_free')
            ->allowEmptyString('is_free');

        $validator
            ->boolean('is_active')
            ->allowEmptyString('is_active');

        $validator
            ->integer('order_position')
            ->allowEmptyString('order_position');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['title']), ['errorField' => 'title']);

        return $rules;
    }

    // Finder methods
    public function findActive(Query $query, array $options)
    {
        return $query->where(['is_active' => true]);
    }

    public function findByCategory(Query $query, array $options)
    {
        if (isset($options['category'])) {
            return $query->where(['category' => $options['category']]);
        }
        return $query;
    }

    public function findByDifficulty(Query $query, array $options)
    {
        if (isset($options['difficulty'])) {
            return $query->where(['difficulty' => $options['difficulty']]);
        }
        return $query;
    }

    public function findFree(Query $query, array $options)
    {
        return $query->where(['is_free' => true]);
    }

    public function findOrdered(Query $query, array $options)
    {
        return $query->order(['order_position' => 'ASC', 'created' => 'DESC']);
    }

    // Custom methods
    public function getCategories()
    {
        return $this->find()
            ->select(['category'])
            ->where(['category IS NOT' => null, 'category !=' => ''])
            ->distinct(['category'])
            ->toArray();
    }

    public function getDifficulties()
    {
        return ['Iniciante', 'Intermediário', 'Avançado'];
    }
}