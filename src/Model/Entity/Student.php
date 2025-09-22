<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class Student extends Entity
{
    protected $_accessible = [
        'name' => true,
        'email' => true,
        'created' => true,
        'modified' => true,
        'studies' => true,
    ];

    protected $_hidden = [
        'id',
    ];
}