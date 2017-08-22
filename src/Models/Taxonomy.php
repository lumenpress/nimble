<?php 

namespace Lumenpress\ORM\Models;

use Illuminate\Support\Facades\Schema;
use Lumenpress\ORM\Builders\TaxonomyBuilder;
use Lumenpress\ORM\Collections\TaxonomyCollection;

class Taxonomy extends Model
{
    /**
     * [$taxonomyPost description]
     * @var array
     */
    protected static $registeredTaxonomies;

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

    protected $appends = [
        'id', 'name', 'slug', 'group', 'order'
    ];

    /**
     * [$hidden description]
     * @var [type]
     */
    protected $hidden = [
        'term_taxonomy_id',
        'term',
    ];

    protected $with = ['term'];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->count = 0;
        $this->parent = 0;
        $this->term_taxonomy_id = 0;

        if (property_exists($this, 'taxonomy')) {
            $this->attributes['taxonomy'] = $this->taxonomy;
        }
    }

    /**
     * Override newCollection() to return a custom collection.
     *
     * @param array $models
     *
     * @return \Lumenpress\ORM\PostMetaCollection
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
            $builder->where('taxonomy', $this->taxonomy);
        }

        // $builder->orderBy('taxonomy');

        // d(Schema::hasColumn('terms', 'term_order'));

        // $builder->orderBy('term_order');

        return $builder;
    }

    /**
     * Set the specific relationship in the model.
     *
     * @param  string  $relation
     * @param  mixed  $value
     * @return $this
     */
    public function setRelation($relation, $value)
    {
        if (method_exists($value, 'setObject')) {
            $value->setObject($this);
        }

        $this->relations[$relation] = $value;

        return $this;
    }

    /**
     * [term description]
     * @return [type] [description]
     */
    public function term()
    {
        return $this->hasOne(Term::class, 'term_id', 'term_id');
    }

    /**
     * Meta data relationship.
     *
     * @return Lumenpress\ORM\TermMetaCollection
     */
    public function meta()
    {
        return $this->term->meta();
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
     * Mutator for order attribute.
     *
     * @return TaxonomyTerm
     */
    protected function getTerm($key)
    {
        return $this->term instanceof Term ? $this->term->$key : null;
    }

    /**
     * Mutator for order attribute.
     *
     * @return void
     */
    protected function setTerm($key, $value)
    {
        if (!$this->term) {
            $this->relations['term'] = new Term;
        }
        $this->term->$key = $value;
    }

    /**
     * Accessor for name attribute.
     *
     * @return returnType
     */
    public function getNameAttribute($value)
    {
        return $this->getTerm('name');
    }

    /**
     * Mutator for name attribute.
     *
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->setTerm('name', $value);
    }

    /**
     * Accessor for slug attribute.
     *
     * @return returnType
     */
    public function getSlugAttribute($value)
    {
        return $this->getTerm('slug');
    }

    /**
     * Mutator for slug attribute.
     *
     * @return void
     */
    public function setSlugAttribute($value)
    {
        $this->setTerm('slug', $value);
    }

    /**
     * Accessor for group attribute.
     *
     * @return returnType
     */
    public function getGroupAttribute($value)
    {
        return $this->getTerm('group');
    }

    /**
     * Mutator for group attribute.
     *
     * @return void
     */
    public function setGroupAttribute($value)
    {
        $this->setTerm('group', $value);
    }

    /**
     * Accessor for order attribute.
     *
     * @return returnType
     */
    public function getOrderAttribute($value)
    {
        return $this->getTerm('order');
    }

    /**
     * Mutator for order attribute.
     *
     * @return void
     */
    public function setOrderAttribute($value)
    {
        $this->setTerm('order', $value);
    }

    /**
     * Accessor for parentId attribute.
     *
     * @return returnType
     */
    public function getParentIdAttribute($value)
    {
        return array_get($this->attributes, 'parent', 0);
    }

    /**
     * Mutator for parentId attribute.
     *
     * @return void
     */
    public function setParentIdAttribute($value)
    {
        $this->attributes['parent'] = $value;
    }

    /**
     * Accessor for id attribute.
     *
     * @return returnType
     */
    public function getIdAttribute($value)
    {
        return $this->term_taxonomy_id;
    }

    /**
     * Mutator for id attribute.
     *
     * @return void
     */
    public function setIdAttribute($value)
    {
        $this->attributes['term_taxonomy_id'] = $value;
    }

    public function save(array $options = [])
    {
        if (!$this->taxonomy) {
            throw new \Exception("Invalid taxonomy.");
        }

        if (!$this->term_taxonomy_id && static::exists($this->name, $this->parentId, $this->taxonomy)) {
            throw new \Exception('A term with the name provided already exists with this parent.');
        }

        if (!$this->term->save()) {
            return false;
        }

        $this->term_id = $this->term->term_id;

        return parent::save($options);
    }

    public static function registerTaxonomy($type, $class)
    {
        static::$registeredTaxonomies[$type] = $class;
    }

    public static function getTermClassByTaxonomy($type)
    {
        return array_get(static::$registeredTaxonomies, $type, Term::class);
    }
}
