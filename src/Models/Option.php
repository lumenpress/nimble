<?php

namespace LumenPress\Nimble\Models;

use LumenPress\Nimble\Concerns\TrySerialize;
use LumenPress\Nimble\Collections\OptionCollection;

class Option extends Model
{
    use TrySerialize;

    protected static $instance;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'options';

    /**
     * The primary key of the model.
     *
     * @var string
     */
    protected $primaryKey = 'option_id';

    /**
     * [$timestamps description].
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Override newCollection() to return a custom collection.
     *
     * @param array $models
     *
     * @return \LumenPress\Nimble\Collections\OptionCollection
     */
    public function newCollection(array $models = [])
    {
        return (new OptionCollection($models))->setRelated($this);
    }

    /**
     * Accessor for key attribute.
     *
     * @return returnType
     */
    public function getKeyAttribute($value)
    {
        return $this->option_name;
    }

    /**
     * Mutator for key attribute.
     *
     * @return void
     */
    public function setKeyAttribute($value)
    {
        $this->attributes['option_name'] = $value;
    }

    /**
     * Accessor for value attribute.
     *
     * @return returnType
     */
    public function getValueAttribute($value)
    {
        return $this->option_value;
    }

    /**
     * Mutator for value attribute.
     *
     * @return void
     */
    public function setValueAttribute($value)
    {
        $this->option_value = $value;
    }

    /**
     * Accessor for OptionValue attribute.
     *
     * @return returnType
     */
    public function getOptionValueAttribute($value)
    {
        return $this->trySerialize($value);
    }

    /**
     * Mutator for OptionValue attribute.
     *
     * @return void
     */
    public function setOptionValueAttribute($value)
    {
        $this->attributes['option_value'] = is_array($value) ? serialize($value) : $value;
    }

    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = static::all();
        }

        return static::$instance;
    }
}
