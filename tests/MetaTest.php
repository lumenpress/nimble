<?php 

namespace Lumenpress\ORM\Tests;

use Lumenpress\ORM\Models\Post;
use Lumenpress\ORM\Models\Meta;

class MetaTest extends TestCase
{
    public function testMetaRelatedCollection()
    {
        $post = new Post;

        $post->title = 'test meta related collection';
        $post->save();

        $meta = Meta::table('postmeta')->objectKey($post->id)->get();

        $this->assertEquals(count($post->meta), count($meta));

        $meta[] = [
            'key' => 'text',
            'value' => 'value1',
            'object_id' => $post->id,
        ];

        $meta->save();

        $post = Post::find($post->id);
        $meta = Meta::table('postmeta')->objectKey($post->id)->get();

        $this->assertEquals($post->meta->text, 'value1');
        $this->assertEquals(count($post->meta), count($meta));

        $meta = Meta::table('postmeta')->objectKey($post->id)->get();

        $meta[0] = [
            // 'key' => 'text', 
            'value' => 'value2',
            // 'object_id' => $post->id,
        ];

        $meta->save();

        $this->assertEquals($meta->text, 'value2');
    }
}