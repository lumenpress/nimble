<?php 

namespace Lumenpress\ORM\Collections;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractCollection extends Collection
{
    /**
     * [$itemClass description]
     * @var [type]
     */
    protected $itemClass;

    /**
     * [$changedKeys description]
     * @var array
     */
    protected $changedKeys = [];

    /**
     * [$extraItems description]
     * @var array
     */
    protected $extraItems = [];

    protected $object;

    /**
     * [save description]
     * @param  Model  $object [description]
     * @return [type]         [description]
     */
    abstract public function save(Model $object);

    public function __toString()
    {
        return '';
    }

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

    public function isNeedSave()
    {
        return !empty($this->items) || !empty($this->extraItems);
    }

    public function setObject($object)
    {
        $this->object = $object;
    }

    protected function setItemClass($itemClass)
    {
        $this->itemClass = $itemClass;
    }

    public static function create(array $models, $itemClass)
    {
        $collection = new static($models);
        $collection->setItemClass($itemClass);
        return $collection;
    }

}
