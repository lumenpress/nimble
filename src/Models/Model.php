<?php 

namespace Lumenpress\ORM\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    protected $aliases = [];

    /**
     * Determine if an attribute or relation exists on the model.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        if (isset($this->aliases[$key])) {
            $key = $this->aliases[$key];
        }
        return ! is_null($this->getAttribute($key));
    }

    /**
     * Unset an attribute on the model.
     *
     * @param  string  $key
     * @return void
     */
    public function __unset($key)
    {
        if (isset($this->aliases[$key])) {
            $key = $this->aliases[$key];
        }
        unset($this->attributes[$key], $this->relations[$key]);
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->aliases[$key])) {
            $key = $this->aliases[$key];
        }
        return $this->getAttribute($key);
    }

    /**
     * Dynamically set attributes on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function __set($key, $value)
    {
        if (isset($this->aliases[$key])) {
            $key = $this->aliases[$key];
        }
        $this->setAttribute($key, $value);
    }

    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();
        foreach ($this->aliases as $aliasKey => $attributeKey) {
            if (isset($this->$attributeKey)) {
                if (in_array($attributeKey, $this->dates)) {
                    $attributes[$aliasKey] = (string) $this->$attributeKey;
                } else {
                    $attributes[$aliasKey] = $this->$attributeKey;
                }
            }
        }
        return $attributes;
    }

    public function __toString()
    {
        return '';
    }
}
