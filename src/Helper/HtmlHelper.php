<?php
declare(strict_types=1);

namespace App\Helper;

class HtmlHelper
{
    public function css($path)
    {
        return '<link rel="stylesheet" href="' . $path . '">';
    }
    
    public function script($path)
    {
        return '<script src="' . $path . '"></script>';
    }
    
    public function link($text, $url, $options = [])
    {
        $class = isset($options['class']) ? ' class="' . $options['class'] . '"' : '';
        return '<a href="' . $url . '"' . $class . '>' . $text . '</a>';
    }
}