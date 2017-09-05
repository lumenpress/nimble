<?php

namespace Lumenpress\ORM\Tests;

use Lumenpress\ORM\Models\Taxonomy;
use Illuminate\Database\Eloquent\Collection;

class TermTaxonomyTest extends TestCase
{
    public function testTax()
    {
        $tax = new Taxonomy;
        $tax->taxonomy = 'category';
        $tax->name = 'Main2';
        $this->assertTrue($tax->save());
    }

    public function testTaxAliases()
    {
        $tax = new Taxonomy;
        $tax->taxonomy = 'category';
        $tax->name = 'test tax aliases';
        $tax->save();

        foreach ($tax->getAliases() as $aliase => $original) {
            $this->assertEquals($tax->$aliase, $tax->$original, $original);
        }
    }

    public function testMeta()
    {
        $tax = new Taxonomy;
        $tax->name = 'test meta';
        $tax->taxonomy = 'category';

        $this->assertFalse(isset($tax->meta->text));

        $tax->meta->text = 'Text1';
        $this->assertTrue(isset($tax->meta->text));

        $tax->save();

        $category = Taxonomy::find($tax->id);
        $this->assertEquals($category->meta->text, 'Text1');
        unset($category->meta->text);
        $this->assertFalse(isset($category->meta->text));
    }

    public function testTaxQueryBuilder()
    {
        $this->assertSame(Taxonomy::taxonomy('category')->first()->toArray(), Taxonomy::where('taxonomy', 'category')->first()->toArray());
        $this->assertInstanceOf(Collection::class, Taxonomy::get());
    }
}
