<?php 

namespace Lumenpress\ORM\Tests;

use Lumenpress\ORM\Models\Post;
use Lumenpress\ORM\Collections\RelatedCollection;
use Lumenpress\ORM\Relations\HasMeta;
use Illuminate\Database\Eloquent\Relations\Relation;

/**
 * PostMetaTest
 *
 * @group group
 */
class PostMetaTest extends TestCase
{
    public function testMeta()
    {
        $post = new Post;
        $post->title = 'test post meta';

        $text = 'This is a text';
        
        $this->assertFalse(isset($post->meta->text));

        $post->meta->text = $text;
        
        $this->assertTrue(isset($post->meta->text));
        $this->assertEquals($post->meta->text, $text);

        $post->save();

        $post = Post::find($post->ID);

        $this->assertTrue(isset($post->meta->text));
        $this->assertEquals($post->meta->text, $text);
    }

    public function testArrayMeta()
    {
        $post = new Post;
        $post->title = 'test post meta';

        $arr = [1,2,3];

        $this->assertInstanceOf(RelatedCollection::class, $post->meta);

        $post->meta->arr = $arr;

        $this->assertEquals($post->meta->arr, $arr);
        $this->assertTrue(is_array($post->meta->arr));

        $post->save();

        $post = Post::find($post->ID);

        $this->assertTrue(isset($post->meta->arr));
        $this->assertSame($post->meta->arr, $arr);
    }

    public function testDeleteMeta()
    {
        $post = new Post;
        $post->title = 'test delete post meta';
        $post->meta->text = 'Text1';
        $post->save();

        unset($post->meta->text);
        $this->assertFalse(isset($post->meta->text));

        $post->save();
        $this->assertFalse(isset($post->meta->text));

        $post = Post::find($post->ID);
        $this->assertFalse(isset($post->meta->text));
    }

    public function testMetaQueryBuilder()
    {
        $post = new Post;
        $post->title = 'post meta query builder';
        $post->meta->text = 'Text1';
        $post->save();

        $this->assertInstanceOf(Relation::class, $post->meta());
        $this->assertEquals('Text1', $post->meta('text')->value());

        $post->meta('text')->value('Text2')->push();

        $this->assertEquals('Text2', $post->meta('text')->value());
    }
}
