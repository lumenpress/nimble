<?php 

namespace Lumenpress\ORM\Models;

use Lumenpress\ORM\Builders\MetaBuilder;
use Lumenpress\ORM\Collections\MetaCollection;

class Meta extends Model
{
    public $timestamps = false;

    protected $objectKey;

    protected $primaryKey = 'meta_id';

    protected $aliases = [
        'id' => 'meta_id',
        'key' => 'meta_key',
        'value' => 'meta_value'
    ];

    protected $hidden = [
        'meta_id',
        'meta_key',
        'meta_value',
    ];

    /**
     * Override newCollection() to return a custom collection.
     *
     * @param array $models
     *
     * @return \Lumenpress\ORM\PostMetaCollection
     */
    public function newCollection(array $models = [])
    {
        return (new MetaCollection($models))->setRelated($this);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        return new MetaBuilder($query);
    }

    /**
     * Create a new instance of the given model.
     *
     * @param  array  $attributes
     * @param  bool  $exists
     * @return static
     */
    public function newInstance($attributes = [], $exists = false)
    {
        $model = parent::newInstance($attributes, $exists);
        $model->setTable($this->table);
        $model->setObjectKeyName($this->objectKey);
        return $model;
    }

    /**
     * Accessor for metaValue attribute.
     *
     * @return returnType
     */
    public function getMetaValueAttribute($value)
    {
        return lumenpress_is_serialized($value) ? unserialize($value) : $value;
    }

    /**
     * Mutator for metaValue attribute.
     *
     * @return void
     */
    public function setMetaValueAttribute($value)
    {
        $this->attributes['meta_value'] = is_array($value) ? serialize($value) : $value;
    }

    public function getObjectKeyName()
    {
        return $this->objectKey;
    }

    public function setObjectKeyName($key)
    {
        $this->objectKey = $key;

        $this->addAliases(['object_id' => $key]);
        $this->addHidden($key);

        return $this;
    }

    public function __toString()
    {
        return '';
    }

}
