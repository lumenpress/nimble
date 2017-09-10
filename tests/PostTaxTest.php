<?php

namespace LumenPress\Nimble\Tests;

use LumenPress\Nimble\Models\Tag;
use LumenPress\Nimble\Models\Post;
use Illuminate\Support\Collection;
use LumenPress\Nimble\Models\Category;
use LumenPress\Nimble\Collections\RelatedCollection;

class PostTaxTest extends TestCase
{
    public function testTax()
    {
        $post = new Post();
        $post->title = 'test post taxonomies';

        $this->assertInstanceOf(RelatedCollection::class, $post->tax);
        $this->assertFalse(isset($post->tax->category));

        $post->tax->category = 'Main';

        $this->assertInstanceOf(Collection::class, $post->tax->category);
        $this->assertEquals(count($post->tax->category), 1);

        $post->save();
    }

    public function testMultipleTaxonomies()
    {
        $categories = ['category1', 'category2'];
        $tags = 'tag1';

        $post = new Post();
        $post->title = 'test multiple taxonomies';

        $post->tax->category = $categories;
        $post->tax->post_tag = $tags;

        $post->save();

        $this->assertCount(count($categories), $post->tax->category, 'message');
        $this->assertCount(1, $post->tax->post_tag, 'message');
        foreach ($post->tax->category as $category) {
            $this->assertInstanceOf(Category::class, $category, 'message');
        }

        foreach ($post->tax->post_tag as $tag) {
            $this->assertInstanceOf(Tag::class, $tag, 'message');
        }

        $post = Post::find($post->id);

        $this->assertCount(count($categories), $post->tax->category, 'message');
        $this->assertCount(1, $post->tax->post_tag, 'message');

        foreach ($post->tax->category as $category) {
            $this->assertInstanceOf(Category::class, $category, 'message');
        }

        foreach ($post->tax->post_tag as $tag) {
            $this->assertInstanceOf(Tag::class, $tag, 'message');
        }
    }
}
