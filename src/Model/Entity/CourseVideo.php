<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class CourseVideo extends Entity
{
    protected $_accessible = [
        'course_id' => true,
        'title' => true,
        'description' => true,
        'video_url' => true,
        'video_type' => true,
        'duration_seconds' => true,
        'order_position' => true,
        'is_preview' => true,
        'is_active' => true,
        'created' => true,
        'modified' => true,
        'course' => true,
        'student_progress' => true,
    ];

    protected $_hidden = [
        'id',
    ];

    // Virtual fields
    protected function _getFormattedDuration()
    {
        if ($this->duration_seconds < 60) {
            return $this->duration_seconds . 's';
        }
        
        $minutes = floor($this->duration_seconds / 60);
        $seconds = $this->duration_seconds % 60;
        
        if ($minutes < 60) {
            return $minutes . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
        }
        
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        
        return $hours . ':' . str_pad($minutes, 2, '0', STR_PAD_LEFT) . ':' . str_pad($seconds, 2, '0', STR_PAD_LEFT);
    }

    protected function _getEmbedUrl()
    {
        if ($this->video_type === 'youtube') {
            // Converter URL do YouTube para embed
            $videoId = $this->extractYouTubeId($this->video_url);
            if ($videoId) {
                return "https://www.youtube.com/embed/{$videoId}";
            }
        }
        
        if ($this->video_type === 'vimeo') {
            // Converter URL do Vimeo para embed
            $videoId = $this->extractVimeoId($this->video_url);
            if ($videoId) {
                return "https://player.vimeo.com/video/{$videoId}";
            }
        }
        
        return $this->video_url;
    }

    private function extractYouTubeId($url)
    {
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
        return $matches[1] ?? null;
    }

    private function extractVimeoId($url)
    {
        preg_match('/vimeo\.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|)(\d+)(?:$|\/|\?)/', $url, $matches);
        return $matches[3] ?? null;
    }
}