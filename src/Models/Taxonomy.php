<?php

namespace Lumenpress\Fluid\Models;

use Illuminate\Support\Facades\Schema;
use Lumenpress\Fluid\Concerns\RegisterTypes;
use Lumenpress\Fluid\Builders\TaxonomyBuilder;
use Lumenpress\Fluid\Concerns\TaxonomyAttributes;

class Taxonomy extends Model
{
    use RegisterTypes, TaxonomyAttributes;

    /**
     * [$taxonomyPost description].
     *
     * @var array
     */
    protected static $registeredTypes = [
        'category' => Category::class,
        'post_tag' => Tag::class,
    ];

    protected static $termClass = Term::class;

    /**
     * [$table description].
     *
     * @var string
     */
    protected $table = 'term_taxonomy';

    /**
     * [$primaryKey description].
     *
     * @var string
     */
    protected $primaryKey = 'term_taxonomy_id';

    /**
     * [$timestamps description].
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * [$with description].
     *
     * @var array
     */
    protected $with = ['term'];

    /**
     * [$hidden description].
     *
     * @var [type]
     */
    protected $hidden = [
        'term_taxonomy_id',
        'term',
    ];

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = [
        'taxonomy',
    ];

    /**
     * [$aliases description].
     *
     * @var [type]
     */
    protected $aliases = [
        'id'   => 'term_taxonomy_id',
        'type' => 'taxonomy',
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
     * @param \Illuminate\Database\Query\Builder $query
     *
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
     * Create a new model instance that is existing.
     *
     * @param array       $attributes
     * @param string|null $connection
     *
     * @return static
     */
    public function newFromBuilder($attributes = [], $connection = null)
    {
        $attributes = (array) $attributes;

        if (isset($attributes['taxonomy'])) {
            $model = $this->newInstance(['taxonomy' => $attributes['taxonomy']], true);
        } else {
            $model = $this->newInstance([], true);
        }

        $model->setRawAttributes($attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());

        return $model;
    }

    /**
     * Create a new instance of the given model.
     *
     * @param array $attributes
     * @param bool  $exists
     *
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $attributes = (array) $attributes;

        $taxonomy = isset($attributes['taxonomy']) ? $attributes['taxonomy'] : '';
        $class = static::getClassNameByType($taxonomy, static::class);

        // This method just provides a convenient way for us to generate fresh model
        // instances of this current model. It is particularly useful during the
        // hydration of new objects via the Eloquent query builder instances.
        $model = new $class($attributes);

        $model->exists = $exists;

        $model->setConnection(
            $this->getConnectionName()
        );

        return $model;
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
     * [save description].
     *
     * @param array $options [description]
     *
     * @return [type] [description]
     */
    public function save(array $options = [])
    {
        if (! $this->taxonomy) {
            throw new \Exception('Invalid taxonomy.');
        }

        if (! $this->term_taxonomy_id && static::exists($this->name, $this->taxonomy, $this->parent_id)) {
            throw new \Exception('A term with the name provided already exists with this parent.');
        }

        if (is_null($this->name)) {
            throw new \Exception('name is invalid', 1);
        }

        if (! $this->term->save()) {
            return false;
        }

        $this->term_id = $this->term->term_id;

        return parent::save($options);
    }

    public static function setTermClass($termClass)
    {
        static::$termClass = $termClass;
    }
}
