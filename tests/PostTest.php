<?php

namespace LumenPress\Nimble\Tests;

use Carbon\Carbon;
use Illuminate\Support\Str;
use LumenPress\Nimble\Models\Post;

class PostTest extends TestCase
{
    public function testCreatingPost()
    {
        $post = new Post();
        $post->title = 'test creating post';

        $this->assertTrue($post->save());
    }

    public function testCreatingPage()
    {
        $post = new Post();
        $post->type = 'page';
        $post->title = 'test creating page';
        $post->save();

        $page = Post::find($post->ID);

        $this->assertTrue($post->type == 'page' && $page->type == 'page');
    }

    public function testPostSlug()
    {
        $title = 'test post slug';

        for ($i = 1; $i < 6; $i++) {
            $post = new Post();
            $post->title = $title;
            $post->save();
            $this->assertTrue($post->post_name == $post->slug);
            $this->assertTrue(Str::slug($title).($i == 1 ? '' : '-'.$i) == $post->slug);
        }
    }

    public function testPostDates()
    {
        $post = new Post();
        $post->title = 'test post timestamps';
        // $post->author_id = 1;
        $post->save();

        $this->assertEquals($post->type, 'post');
        $this->assertInstanceOf(Carbon::class, $post->created_at);
        $this->assertInstanceOf(Carbon::class, $post->created_at_gmt);
        $this->assertInstanceOf(Carbon::class, $post->updated_at);
        $this->assertInstanceOf(Carbon::class, $post->updated_at_gmt);

        $this->assertEquals((string) $post->created_at->timezone('UTC'), (string) $post->created_at_gmt);
        $this->assertEquals((string) $post->updated_at->timezone('UTC'), (string) $post->updated_at_gmt);

        $post = Post::find($post->ID);

        $this->assertEquals($post->type, 'post');
        $this->assertInstanceOf(Carbon::class, $post->created_at);
        $this->assertInstanceOf(Carbon::class, $post->created_at_gmt);
        $this->assertInstanceOf(Carbon::class, $post->updated_at);
        $this->assertInstanceOf(Carbon::class, $post->updated_at_gmt);

        $this->assertEquals($post->created_at->timezone('UTC'), $post->created_at_gmt);
        $this->assertEquals($post->updated_at->timezone('UTC'), $post->updated_at_gmt);
    }

    public function testPostAliases()
    {
        $post = new Post();
        $post->title = 'test post aliases';
        // $post->author_id = 1;
        $post->save();

        foreach ($post->getAliases() as $aliase => $original) {
            $this->assertEquals($post->$aliase, $post->$original, $original);
        }
    }

    /**
     * @covers class::()
     */
    public function testDefaultAttributes()
    {
        $post = new Post();
        $post->title = 'test post default attributes';
        $post->save();
        $defaults = [
            'post_author'           => 0,
            'post_content'          => '',
            'post_excerpt'          => '',
            'post_status'           => 'publish',
            'comment_status'        => 'open',
            'ping_status'           => 'open',
            'to_ping'               => '',
            'pinged'                => '',
            'post_content_filtered' => '',
            'post_parent'           => 0,
            'menu_order'            => 0,
            'post_type'             => 'post',
            'comment_count'         => 0,
        ];
        foreach ($defaults as $key => $value) {
            $this->assertEquals($post->$key, $value, $key);
        }
        $post = Post::find($post->ID);
        foreach ($defaults as $key => $value) {
            $this->assertEquals($post->$key, $value, $key);
        }
    }
}
