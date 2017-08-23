<?php 

namespace Lumenpress\ORM\Models\Concerns;

trait HasAliases
{
    /**
     * The attribute's aliases.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * Convert the model's attributes to an array.
     *
     * @return array
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();
        foreach ($this->aliases as $aliasKey => $originalKey) {
            if (isset($this->$originalKey)) {
                $attributes[$aliasKey] = in_array($originalKey, $this->dates) 
                    ? (string) $this->getAttribute($originalKey)
                    : $this->getAttribute($originalKey);
            }
        }
        return $attributes;
    }

    /**
     * Get an attribute from the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getAttribute($key)
    {
        if (isset($this->aliases[$key]) && !$this->hasGetMutator($key)) {
            $key = $this->aliases[$key];
            if (stripos($key, '.') !== false) {
                $keys = explode('.', $key);
                $relation = $this->getRelationValue(array_shift($keys));
                return $relation->{$keys[0]};
            }
        }
        return parent::getAttribute($key);
    }

    /**
     * Set a given attribute on the model.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return $this
     */
    public function setAttribute($key, $value)
    {
        if (isset($this->aliases[$key]) && !$this->hasSetMutator($key)) {
            $key = $this->aliases[$key];
            if (stripos($key, '.') !== false) {
                $keys = explode('.', $key);
                $relation = $this->getRelationValue(array_shift($keys));
                if ($relation) {
                    $relation->{$keys[0]} = $value;
                }
                unset($keys, $relation);
                return $this;
            }
        }
        return parent::setAttribute($key, $value);
    }

    public function unsetAttribute($key)
    {
        if (isset($this->aliases[$key])) {
            $key = $this->aliases[$key];
            if (stripos($key, '.') !== false) {
                $keys = explode('.', $key);
                $relation = $this->getRelationValue(array_shift($keys));
                if ($relation) {
                    unset($relation->{$keys[0]});
                }
                unset($keys, $relation);
                return;
            }
        }
        unset($this->attributes[$key], $this->relations[$key]);
    }

    /**
     * Set the specific relationship in the model.
     *
     * @param  string  $relation
     * @param  mixed  $value
     * @return $this
     */
    public function setRelation($relation, $value)
    {
        if (method_exists($value, 'setRelatedParent')) {
            $value->setRelatedParent($this);
        }

        $this->relations[$relation] = $value;

        return $this;
    }

}