<?php 

namespace Lumenpress\ORM\Models;

class PostMeta extends AbstractMeta
{
    /**
     * [$table description]
     * @var string
     */
    protected $table = 'postmeta';

    /**
     * [$relationKey description]
     * @var string
     */
    protected $objectKey = 'post_id';
}
