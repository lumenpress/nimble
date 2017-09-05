<?php

namespace Lumenpress\ORM\Models;

use Lumenpress\ORM\Collections\TaxonomyCollection;

class PostTaxonomy extends Taxonomy
{
    /**
     * Override newCollection() to return a custom collection.
     *
     * @param array $models
     *
     * @return \Lumenpress\ORM\PostMetaCollection
     */
    public function newCollection(array $models = [])
    {
        return (new TaxonomyCollection($models))->setRelated($this);
    }
}
