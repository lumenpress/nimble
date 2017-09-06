<?php

namespace Lumenpress\ORM\Tests;

use Lumenpress\ORM\Models\Meta;
use Lumenpress\ORM\Models\Post;

class MetaTest extends TestCase
{
    public function testMetaRelatedCollection()
    {
        $post = new Post();

        $post->title = 'test meta related collection';
        $post->save();

        $meta = Meta::table('postmeta')->objectKey($post->id)->get();

        $this->assertEquals(count($post->meta), count($meta));

        $meta[] = [
            'key'       => 'text',
            'value'     => 'value1',
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

    public function testCreatingMeta()
    {
        $meta = new Meta();
        $meta->table = 'postmeta';
        $meta->object_id = 1;
        $meta->key = 'key1';
        $meta->value = 'key1value1';
        $this->assertTrue($meta->save());

        $post = Post::find(1);
        $this->assertEquals('key1value1', $post->meta->key1);
    }
}
