<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Course extends Entity
{
    protected $_accessible = [
        'title' => true,
        'description' => true,
        'thumbnail' => true,
        'duration_minutes' => true,
        'difficulty' => true,
        'category' => true,
        'instructor' => true,
        'price' => true,
        'is_free' => true,
        'is_active' => true,
        'order_position' => true,
        'created' => true,
        'modified' => true,
        'course_videos' => true,
        'course_enrollments' => true,
    ];

    protected $_hidden = [
        'id',
    ];

    // Virtual fields
    protected function _getDifficultyBadge()
    {
        $badges = [
            'Iniciante' => 'success',
            'Intermediário' => 'warning', 
            'Avançado' => 'danger'
        ];
        
        return $badges[$this->difficulty] ?? 'secondary';
    }

    protected function _getFormattedPrice()
    {
        if ($this->is_free) {
            return 'Gratuito';
        }
        
        return 'R$ ' . number_format($this->price, 2, ',', '.');
    }

    protected function _getFormattedDuration()
    {
        if ($this->duration_minutes < 60) {
            return $this->duration_minutes . ' min';
        }
        
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        if ($minutes > 0) {
            return $hours . 'h ' . $minutes . 'min';
        }
        
        return $hours . 'h';
    }
}