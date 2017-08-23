<?php 

namespace Lumenpress\ORM\Models;

class CommentMeta extends AbstractMeta
{
    /**
     * [$table description]
     * @var string
     */
    protected $table = 'commentmeta';

    /**
     * [$relationKey description]
     * @var string
     */
    protected $objectKey = 'comment_id';
}
