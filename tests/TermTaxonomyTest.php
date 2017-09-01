<?php 

namespace Lumenpress\ORM\Tests;

use Lumenpress\ORM\Models\Taxonomy;

class TermTaxonomyTest extends TestCase
{
    public function testTax()
    {
        $term = new Taxonomy;
        $term->taxonomy = 'category';
        $term->name = 'Main2';
        $this->assertTrue($term->save());
    }
}
