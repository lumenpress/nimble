<?php

namespace Lumenpress\Fluid\Models;

use Lumenpress\Fluid\Collections\TaxonomyCollection;

class PostTaxonomy extends Taxonomy
{
    /**
     * Override newCollection() to return a custom collection.
     *
     * @param array $models
     *
     * @return \Lumenpress\Fluid\PostMetaCollection
     */
    public function newCollection(array $models = [])
    {
        return (new TaxonomyCollection($models))->setRelated($this);
    }
}
