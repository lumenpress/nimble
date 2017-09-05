<?php

namespace Lumenpress\ORM\Models;

use Lumenpress\ORM\Relations\HasMeta;
use Lumenpress\ORM\Builders\TermBuilder;
use Lumenpress\ORM\Collections\RelatedCollection;

class Term extends Model
{
    /**
     * [$table description]
     * @var string
     */
    protected $table = 'terms';

    /**
     * [$primaryKey description]
     * @var string
     */
    protected $primaryKey = 'term_id';

    /**
     * [$timestamps description]
     * @var boolean
     */
    public $timestamps = false;

    /**
     * [$appends description]
     * @var [type]
     */
    protected $appends = [
        // 'id',
        // 'group',
        // 'order',
    ];

    /**
     * [$hidden description]
     * @var [type]
     */
    protected $hidden = [
        'term_id',
        // 'term_order',
        'term_group',
    ];

    protected $aliases = [
        'id' => 'term_id',
        'group' => 'term_group',
    ];

    public function __construct(array $attributes = [])
    {
        $this->term_id = 0;
        $this->term_group = 0;

        parent::__construct($attributes);
    }

    public function __toString()
    {
        return $this->name ?: '';
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new TermBuilder($query);
    }

    public function meta($key = null)
    {
        $builder = new HasMeta($this);
        if ($key) {
            $builder->where('meta_key', $key);
        }
        return $builder;
    }

    /**
     * Term belongs to Tax.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tax()
    {
        return $this->belongsTo(Taxonomy::class, 'term_id');
    }

    /**
     * Accessor for taxonomy attribute.
     *
     * @return returnType
     */
    public function getTaxonomyAttribute($value)
    {
        return $this->tax->taxonomy;
    }

    /**
     * Mutator for name attribute.
     *
     * @return void
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->slug = str_slug($value);
    }

    /**
     * Mutator for order attribute.
     *
     * @return void
     */
    public function setOrderAttribute($value)
    {
        // $this->attributes['term_order'] = $value;
    }

    /**
     * Accessor for order attribute.
     *
     * @return returnType
     */
    public function getOrderAttribute($value)
    {
        return 0;
        return $this->term_order;
    }

    public function save(array $options = [])
    {
        if (!$this->slug) {
            $this->slug = str_slug($this->name);
        }
        if (!parent::save($options)) {
            return false;
        }
        foreach ($this->relations as $key => $relation) {
            if ($relation instanceof RelatedCollection) {
                $relation->save();
            }
        }
        return true;
    }

}
