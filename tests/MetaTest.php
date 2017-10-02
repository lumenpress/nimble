<?php

namespace LumenPress\Nimble\Tests;

use LumenPress\Nimble\Models\Meta;
use LumenPress\Nimble\Models\Post;

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
        $post = new Post;
        $post->title = uniqid();
        $post->save();

        $meta = new Meta();
        $meta->table = 'postmeta';
        $meta->object_id = $post->id;
        $meta->key = 'key1';
        $meta->value = $value = 'value '.uniqid();
        $this->assertTrue($meta->save());

        $post = Post::find($post->id);

        $this->assertEquals($value, $post->meta->key1);
    }
}
