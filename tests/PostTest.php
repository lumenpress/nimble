<?php 

namespace Lumenpress\ORM\Tests;

use Lumenpress\ORM\Models\Post;

class PostTest extends TestCase
{
    public function testCreating()
    {
        $post = new Post;
        $post->title = 'abc';
        $post->author_id = 1;
        $post->save();

        $post = new Post;
        $post->type = 'page';
        $post->title = 'abc';
        $post->author_id = 1;

        d($post->save());
    }
}
