<?php 

namespace Lumenpress\ORM\Models;

use Lumenpress\ORM\Builders\TermBuilder;

class TaxonomyTerm extends Model
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
        'id',
        'group',
        'order',
    ];

    /**
     * [$hidden description]
     * @var [type]
     */
    protected $hidden = [
        'term_id',
        'term_order',
        'term_group',
    ];

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        $builder = new TermBuilder($query);

        if (property_exists($this, 'taxonomy') && $this->taxonomy) {
            $builder->taxonomy($this->taxonomy);
        }
        $builder->orderBy('term_order');

        return $builder;
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
     * Accessor for order attribute.
     *
     * @return returnType
     */
    public function getOrderAttribute($value)
    {
        return $this->term_order;
    }

    public function __toString()
    {
        return $this->name ?: '';
    }

}
