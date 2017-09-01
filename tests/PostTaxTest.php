<?php 

namespace Lumenpress\ORM\Tests;

use Illuminate\Support\Collection;
use Lumenpress\ORM\Models\Post;
use Lumenpress\ORM\Models\Meta;
use Lumenpress\ORM\Collections\RelatedCollection;

class PostTaxTest extends TestCase
{
    public function testTax()
    {
        $post = new Post;
        $post->title = 'test post taxonomies';

        $this->assertInstanceOf(RelatedCollection::class, $post->tax);
        $this->assertFalse(isset($post->tax->category));

        $post->tax->category = 'Main';

        $this->assertInstanceOf(Collection::class, $post->tax->category);
        $this->assertEquals(count($post->tax->category), 1);

        $post->save();
    }
}
