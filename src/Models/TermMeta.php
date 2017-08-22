<?php 

namespace Lumenpress\ORM\Models;

class TermMeta extends AbstractMeta
{
    /**
     * [$table description]
     * @var string
     */
    protected $table = 'termmeta';

    /**
     * [$relationKey description]
     * @var string
     */
    protected $objectKey = 'term_id';
}
