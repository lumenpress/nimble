<?php

namespace LumenPress\Nimble\Models;

use LumenPress\Nimble\Collections\TaxonomyCollection;

class PostTaxonomy extends Taxonomy
{
    /**
     * Override newCollection() to return a custom collection.
     *
     * @param array $models
     *
     * @return \LumenPress\Nimble\PostMetaCollection
     */
    public function newCollection(array $models = [])
    {
        return (new TaxonomyCollection($models))->setRelated($this);
    }
}
