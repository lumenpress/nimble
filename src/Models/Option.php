<?php

namespace LumenPress\Nimble\Models;

class Option extends Model
{
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
        $this->attributes['option_value'] = $value;
    }
}
