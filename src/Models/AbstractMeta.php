<?php 

namespace Lumenpress\ORM\Models;

use Lumenpress\ORM\Builders\MetaBuilder;
use Lumenpress\ORM\Collections\MetaCollection;

abstract class AbstractMeta extends Model
{
    protected $objectKey;

    protected $primaryKey = 'meta_id';

    public $timestamps = false;

    protected $aliases = [
        'key' => 'meta_key',
        'value' => 'meta_value'
    ];

    protected $hidden = [
        'post_id',
        'term_id',
        'user_id',
        'meta_id',
        'comment_id',
        'meta_key',
        'meta_value',
    ];

    public function __construct(array $attributes = [])
    {
        if (!$this->objectKey) {
            throw new \Exception("objectKey invalid");
        }
        parent::__construct($attributes);
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
        return MetaCollection::create($models, static::class);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function newEloquentBuilder($query)
    {
        if (property_exists($this, 'builderClass') && !is_null($this->builderClass)) {
            $class = $this->builderClass;
        } else {
            $shortName = substr(strrchr(get_class($this), '\\'), 1);
            $class = "Lumenpress\Builders\\{$shortName}Builder";
        }

        if (!class_exists($class)) {
            $class = MetaBuilder::class;
        }

        return new $class($query);
    }

    /**
     * Accessor for Id attribute.
     *
     * @return returnType
     */
    public function getIdAttribute($value)
    {
        return $this->attributes[$this->primaryKey];
    }

    /**
     * Mutator for ID attribute.
     *
     * @return void
     */
    public function setIdAttribute($value)
    {
        $this->attributes[$this->primaryKey] = $value;
    }

    /**
     * Accessor for oid attribute.
     *
     * @return returnType
     */
    public function getObjectIdAttribute($value)
    {
        return $this->attributes[$this->objectKey];
    }

    /**
     * Mutator for ObjectId attribute.
     *
     * @return void
     */
    public function setObjectIdAttribute($value)
    {
        $this->attributes[$this->objectKey] = $value;
    }

    /**
     * Accessor for metaValue attribute.
     *
     * @return returnType
     */
    public function getMetaValueAttribute($value)
    {
        return lumenpress_is_serialized($this->attributes['meta_value']) 
            ? unserialize($this->attributes['meta_value']) 
            : $this->attributes['meta_value'];
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
        return $this;
    }

    public function __toString()
    {
        return $this->value ?: '';
    }

}
