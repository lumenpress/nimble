<?php

namespace Lumenpress\ORM\Collections;

use Illuminate\Database\Eloquent\Collection as BaseCollection;

abstract class Collection extends BaseCollection implements RelatedCollection
{
    /**
     * [$related description].
     *
     * @var [type]
     */
    protected $related;

    /**
     * [$relatedParent description].
     *
     * @var [type]
     */
    protected $relatedParent;

    /**
     * [$changedKeys description].
     *
     * @var array
     */
    protected $changedKeys = [];

    /**
     * [$extraItems description].
     *
     * @var array
     */
    protected $extraItems = [];

    /**
     * [__isset description].
     *
     * @param [type] $key [description]
     *
     * @return bool [description]
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }

    /**
     * [__get description].
     *
     * @param [type] $key [description]
     *
     * @return [type] [description]
     */
    public function __get($key)
    {
        return $this->offsetGet($key);
    }

    /**
     * [__set description].
     *
     * @param [type] $key   [description]
     * @param [type] $value [description]
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }

    /**
     * [__unset description].
     *
     * @param [type] $key [description]
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }

    /**
     * [__toString description].
     *
     * @return string [description]
     */
    public function __toString()
    {
        return '';
    }

    /**
     * [setRelatedParent description].
     *
     * @param [type] &$relatedParent [description]
     */
    public function setRelatedParent(&$relatedParent)
    {
        $this->relatedParent = $relatedParent;

        return $this;
    }

    /**
     * [setRelated description].
     *
     * @param [type] $related [description]
     */
    public function setRelated($related)
    {
        $this->related = $related;

        return $this;
    }

    /**
     * [save description].
     *
     * @return bool [description]
     */
    abstract public function save();
}
