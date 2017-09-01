<?php 

namespace Lumenpress\ORM\Collections;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Lumenpress\ORM\Concerns\HasRelatedAttributes;

abstract class AbstractCollection extends Collection implements RelatedCollection
{
    use HasRelatedAttributes;

    protected $itemClass;

    protected $changedKeys = [];

    protected $extraItems = [];

    abstract public function save();

    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    public function __toString()
    {
        return '';
    }
}
