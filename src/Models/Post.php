<?php 

namespace Lumenpress\ORM\Models;

use Lumenpress\ORM\Builders\PostBuilder;
use Lumenpress\Acf\Concerns\HasAdvancedCustomFields;
use Lumenpress\Acf\Collections\FieldCollection;

class Post extends AbstractPost
{
    use Concerns\HasPostPaginationAttributes, 
        Concerns\HasPostAttributes;

    protected $postType = 'post';

    protected $with = ['meta'];

    protected $appends = [
        'title',
        'slug',
        'content',
        'excerpt',
        'type',
        'status',
        'link',
    ];
}
