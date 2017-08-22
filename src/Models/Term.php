<?php 

namespace Lumenpress\ORM\Models;

use Lumenpress\ORM\Builders\TermBuilder;

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
        'group',
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

    public function __construct(array $attributes = [])
    {
        $this->append('id');
        parent::__construct($attributes);
        $this->id = 0;
        $this->term_group = 0;
    }

    public function __toString()
    {
        return $this->name ?: '';
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new TermBuilder($query);
    }

    public function meta($key = null)
    {
        $builder = $this->hasMany(TermMeta::class, 'term_id');
        if ($key) {
            $builder->where('meta_key', $key);
        }
        return $builder;
    }

    /**
     * Mutator for id attribute.
     *
     * @return void
     */
    public function setIdAttribute($value)
    {
        $this->attributes['term_id'] = $value;
    }

    /**
     * Accessor for id attribute.
     *
     * @return returnType
     */
    public function getIdAttribute($value)
    {
        return $this->term_id;
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
     * Accessor for name attribute.
     *
     * @return returnType
     */
    public function getNameAttribute($value)
    {
        return $value;
    }

    /**
     * Mutator for group attribute.
     *
     * @return void
     */
    public function setGroupAttribute($value)
    {
        $this->attributes['term_group'] = $value;
    }

    /**
     * Accessor for group attribute.
     *
     * @return returnType
     */
    public function getGroupAttribute($value)
    {
        return $this->term_group;
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
        if (!$this->meta->isEmpty()) {
            return $this->meta->save();
        }
        return true;
    }

}
