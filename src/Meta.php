<?php 

namespace Lumenpress\Models;

use Lumenpress\Models\Collections\MetaCollection;
use Lumenpress\Models\Builders\MetaBuilder;

class Meta extends Model
{

    /**
     * [$primaryKey description]
     * @var string
     */
    protected $primaryKey = 'meta_id';

    /**
     * [$relationKey description]
     * @var string
     */
    protected $objectKey = 'post_id';

    protected $appends = [
        'key',
        'value',
    ];

    protected $hidden = [
        'meta_id',
        'post_id',
        'term_id',
        'user_id',
        'meta_key',
        'meta_value',
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
        return MetaCollection::create($models, static::class);
    }

    /**
     * [newQuery description]
     * @param  boolean $excludeDeleted [description]
     * @return [type]                  [description]
     */
    public function newQuery($excludeDeleted = true)
    {
        if (property_exists($this, 'builderClass') && !is_null($this->builderClass)) {
            $cls = $this->builderClass;
        } else {
            $shortName = substr(strrchr(get_class($this), '\\'), 1);
            $cls = "Lumenpress\Builders\\{$shortName}Builder";
        }

        if (!class_exists($cls)) {
            $cls = MetaBuilder::class;
        }

        $builder = new $cls($this->newBaseQueryBuilder());
        $builder->setModel($this);

        return $builder;
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
     * Accessor for key attribute.
     *
     * @return returnType
     */
    public function getKeyAttribute($value)
    {
        return $this->meta_key;
    }

    /**
     * Mutator for key attribute.
     *
     * @return void
     */
    public function setKeyAttribute($value)
    {
        $this->attributes['meta_key'] = $value;
    }

    /**
     * Accessor for metaValue attribute.
     *
     * @return returnType
     */
    public function getMetaValueAttribute($value)
    {
        return is_serialized($this->attributes['meta_value']) 
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

    /**
     * Accessor for value attribute.
     *
     * @return returnType
     */
    public function getValueAttribute($value)
    {
        return $this->meta_value;
    }

    /**
     * Mutator for value attribute.
     *
     * @return void
     */
    public function setValueAttribute($value)
    {
        $this->meta_value = $value;
    }

    public function __toString()
    {
        return $this->value ?: '';
    }
}
