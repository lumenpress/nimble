<?php 

namespace Lumenpress\Models;

use Lumenpress\Models\Builders\TaxonomyBuilder;
use Lumenpress\Models\Collections\TaxonomyCollection;

class TermTaxonomy extends Model
{
    /**
     * [$taxonomyPost description]
     * @var array
     */
    protected static $taxonomyPost = [];

    /**
     * [$table description]
     * @var string
     */
    protected $table = 'term_taxonomy';

    /**
     * [$primaryKey description]
     * @var string
     */
    protected $primaryKey = 'term_taxonomy_id';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'taxonomy',
    ];

    /**
     * [$timestamps description]
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Override newCollection() to return a custom collection.
     *
     * @param array $models
     *
     * @return \Lumenpress\Models\PostMetaCollection
     */
    public function newCollection(array $models = [])
    {
        return TaxonomyCollection::create($models, static::class);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        $builder = new TaxonomyBuilder($query);

        if (property_exists($this, 'taxonomy') && $this->taxonomy) {
            $builder->is($this->taxonomy);
        }
        $builder->orderBy('taxonomy')->orderBy('order');

        return $builder;
    }

    /**
     * Relationship with Posts model.
     *
     * @return Illuminate\Database\Eloquent\Relations
     */
    public function posts()
    {
        return $this->belongsToMany(static::getPostClassByTaxonomy($this->taxonomy), 
            'term_relationships', 'term_taxonomy_id', 'object_id');
    }

    /**
     * Accessor for TaxonomyId attribute.
     *
     * @return returnType
     */
    public function getTaxonomyIdAttribute($value)
    {
        return $this->term_taxonomy_id;
    }

    public static function getPostClassByTaxonomy($taxonomy)
    {
        if (isset(static::$taxonomyPost[$taxonomy])) {
            return static::$taxonomyPost[$taxonomy];
        }
        return Post::class;
    }

}
