<?php 

namespace Lumenpress\Models;

class Page extends Post
{
    protected $postType = 'page';

    // public function __construct(array $attributes = [])
    // {
    //     unset($this->appends['post_template']);
    //     $this->append(['page_template']);
    //     parent::__construct($attributes);
    // }

}
