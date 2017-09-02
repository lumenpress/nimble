<?php 

namespace Lumenpress\ORM\Models;

use Illuminate\Support\Facades\Schema;
use Lumenpress\ORM\Concerns\RegisterTypes;
use Lumenpress\ORM\Concerns\TaxonomyAttributes;
use Lumenpress\ORM\Builders\TaxonomyBuilder;
use Lumenpress\ORM\Collections\RelatedCollection;

class Taxonomy extends Model
{
    use RegisterTypes, TaxonomyAttributes;

    /**
     * [$taxonomyPost description]
     * @var array
     */
    protected static $registeredTypes = [];

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
     * [$timestamps description]
     * @var boolean
     */
    public $timestamps = false;

    /**
     * [$with description]
     * @var array
     */
    protected $with = ['term'];

    /**
     * [$hidden description]
     * @var [type]
     */
    protected $hidden = [
        'term_taxonomy_id',
        'term',
    ];

    /**
     * [$aliases description]
     * @var [type]
     */
    protected $aliases = [
        'id' => 'term_taxonomy_id',
    ];

    public function __construct(array $attributes = [])
    {
        $this->term_taxonomy_id = 0;
        $this->count = 0;
        $this->parent = 0;
        $this->description = '';

        parent::__construct($attributes);

        if (property_exists($this, 'taxonomy')) {
            $this->setTaxonomyAttribute($this->taxonomy);
        }
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
            $builder->where('taxonomy', $this->taxonomy);
        }

        // $builder->orderBy('taxonomy');

        // d(Schema::hasColumn('terms', 'term_order'));

        // $builder->orderBy('term_order');

        return $builder;
    }

    /**
     * Relationship with Posts model.
     *
     * @return Illuminate\Database\Eloquent\Relations
     */
    public function posts()
    {
        return $this->belongsToMany(Post::class, 'term_relationships', 'term_taxonomy_id', 'object_id');
    }

    /**
     * [save description]
     * @param  array  $options [description]
     * @return [type]          [description]
     */
    public function save(array $options = [])
    {
        if (!$this->taxonomy) {
            throw new \Exception("Invalid taxonomy.");
        }

        if (!$this->term_taxonomy_id && static::exists($this->name, $this->taxonomy, $this->parent_id)) {
            throw new \Exception('A term with the name provided already exists with this parent.');
        }

        if (is_null($this->name)) {
            throw new \Exception("name is invalid", 1);
        }

        if (!$this->term->save()) {
            return false;
        }

        $this->term_id = $this->term->term_id;

        return parent::save($options);
    }
}
