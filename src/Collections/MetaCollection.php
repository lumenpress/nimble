<?php 

namespace Lumenpress\Models\Collections;

use Illuminate\Database\Eloquent\Model;

class MetaCollection extends AbstractCollection
{
    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        if (is_string($key)) {
            foreach ($this->items as $item) {
                if ($item->key == $key) {
                    return true;
                }
            }
            return false;
        } else {
            return array_key_exists($key, $this->items);
        }
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (is_string($key)) {
            foreach ($this->items as $item) {
                if ($item->key == $key) {
                    return $item->value;
                }
            }
        } else {
            return $this->items[$key];
        }
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_string($key)) {
            $this->changedKeys[$key] = true;
            foreach ($this->items as $index => $item) {
                if ($item->key == $key) {
                    $item->value = $value;
                    parent::offsetSet($index, $item);
                    return;
                }
            }
            $cls = $this->itemClass;
            $item = new $cls;
            $item->key = $key;
            $item->value = $value;
            parent::offsetSet(null, $item);
            return;
        }
        parent::offsetSet($key, $value);
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        if (is_string($key)) {
            foreach ($this->items as $index => $item) {
                if ($item->key == $key) {
                    $this->extraItems[] = $item;
                    unset($this->items[$index]);
                    return;
                }
            }
        } else {
            $this->extraItems[] = $this->items[$key];
            unset($this->items[$key]);
        }
    }

    /**
     * [save description]
     * @param  [type] $objectId [description]
     * @return [type]           [description]
     */
    public function save(Model $object)
    {
        $flag = false;
        foreach ($this->items as $item) {
            if (isset($this->changedKeys[$item->key])) {
                $item->objectId = $object->getKey();
                $flag = $item->save() || $flag;
            }
        }
        foreach ($this->extraItems as $item) {
            $flag = $item->delete() || $flag;
        }
        $this->changedKeys = [];
        $this->extraItems = [];
        return $flag;
    }
}
