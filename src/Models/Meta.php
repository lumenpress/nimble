<?php 

namespace Lumenpress\ORM\Models;

use Illuminate\Database\Eloquent\Collection;
use Lumenpress\ORM\Builders\MetaBuilder;
use Lumenpress\ORM\Collections\MetaCollection;
use Lumenpress\ORM\Collections\RelatedCollection;

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
        // foreach ($attributes as $key => $value) {
        //     $model->$key = $value;
        // }
        return $model;
    }

    /**
     * Accessor for table attribute.
     *
     * @return returnType
     */
    public function getTableAttribute($value)
    {
        return $this->table;
    }

    /**
     * Mutator for table attribute.
     *
     * @return void
     */
    public function setTableAttribute($value)
    {
        $this->table = $value;
        $this->setObjectKeyNameThroughTable($value);
    }

    /**
     * Accessor for metaValue attribute.
     *
     * @return returnType
     */
    public function getMetaValueAttribute($value)
    {
        if ($value === 'b:0;') {
            return false;
        }
        if (($result = @unserialize($value)) !== false) {
            return $result;
        }
        return $value;
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

    public function setTableThroughParentTable($parentTable)
    {
        switch ($parentTable) {
            case 'posts':
                $this->setTable('postmeta');
                $this->setObjectKeyName('post_id');
                break;
            case 'terms':
                $this->setTable('termmeta');
                $this->setObjectKeyName('term_id');
                break;
            case 'users':
                $this->setTable('usermeta');
                $this->setObjectKeyName('user_id');
                break;
            case 'comments':
                $this->setTable('commentmeta');
                $this->setObjectKeyName('comment_id');
                break;
        }
    }

    public function setObjectKeyNameThroughTable($table)
    {
        switch ($table) {
            case 'postmeta':
                $this->setObjectKeyName('post_id');
                break;
            case 'termmeta':
                $this->setObjectKeyName('term_id');
                break;
            case 'usermeta':
                $this->setObjectKeyName('user_id');
                break;
            case 'commentmeta':
                $this->setObjectKeyName('comment_id');
                break;
        }
    }

    public function __toString()
    {
        return is_string($this->value) || is_numeric($this->value) ? $this->value : '';
    }

    public static function table($table)
    {
        $meta = new static;
        $meta->setTableAttribute($table);
        // $meta->setTable($table);
        // $meta->setObjectKeyNameThroughTable($table);
        return $meta->newQuery();
    }
}
