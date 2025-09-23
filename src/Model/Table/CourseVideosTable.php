<?php
declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class CourseVideosTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('course_videos');
        $this->setDisplayField('title');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Courses', [
            'foreignKey' => 'course_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('StudentProgress', [
            'foreignKey' => 'video_id',
            'dependent' => true,
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('course_id')
            ->requirePresence('course_id', 'create')
            ->notEmptyString('course_id');

        $validator
            ->scalar('title')
            ->maxLength('title', 255)
            ->requirePresence('title', 'create')
            ->notEmptyString('title');

        $validator
            ->scalar('description')
            ->allowEmptyString('description');

        $validator
            ->scalar('video_url')
            ->maxLength('video_url', 500)
            ->requirePresence('video_url', 'create')
            ->notEmptyString('video_url');

        $validator
            ->scalar('video_type')
            ->inList('video_type', ['youtube', 'vimeo', 'direct', 'embed'])
            ->allowEmptyString('video_type');

        $validator
            ->integer('duration_seconds')
            ->allowEmptyString('duration_seconds');

        $validator
            ->integer('order_position')
            ->allowEmptyString('order_position');

        $validator
            ->boolean('is_preview')
            ->allowEmptyString('is_preview');

        $validator
            ->boolean('is_active')
            ->allowEmptyString('is_active');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['course_id'], 'Courses'), ['errorField' => 'course_id']);

        return $rules;
    }

    // Finder methods
    public function findActive(Query $query, array $options)
    {
        return $query->where(['CourseVideos.is_active' => true]);
    }

    public function findByCourse(Query $query, array $options)
    {
        if (isset($options['course_id'])) {
            return $query->where(['CourseVideos.course_id' => $options['course_id']]);
        }
        return $query;
    }

    public function findOrdered(Query $query, array $options)
    {
        return $query->order(['CourseVideos.order_position' => 'ASC', 'CourseVideos.created' => 'ASC']);
    }

    public function findPreview(Query $query, array $options)
    {
        return $query->where(['CourseVideos.is_preview' => true]);
    }

    // Custom methods
    public function getNextOrderPosition($courseId)
    {
        $lastVideo = $this->find()
            ->where(['course_id' => $courseId])
            ->order(['order_position' => 'DESC'])
            ->first();

        return $lastVideo ? $lastVideo->order_position + 1 : 1;
    }

    public function reorderVideos($courseId, $videoIds)
    {
        $position = 1;
        foreach ($videoIds as $videoId) {
            $this->updateAll(
                ['order_position' => $position],
                ['id' => $videoId, 'course_id' => $courseId]
            );
            $position++;
        }
    }
}