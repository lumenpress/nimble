<?php 

namespace Lumenpress\ORM\Collections;

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
        return isset($this->items[$key]);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return isset($this->items[$key]) ? $this->items[$key]->value : null;
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
        $this->changedKeys[$key] = true;

        if (isset($this->items[$key])) {
            $item = $this->items[$key];
        } else {
            $item = $this->related->newInstance();
            $item->key = $key;
        }

        $item->value = $value;

        $this->items[$key] = $item;
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        if (isset($this->items[$key])) {
            $this->extraItems[] = $this->items[$key];
            unset($this->items[$key]);
        }
    }

    /**
     * [save description]
     * @return [type] [description]
     */
    public function save()
    {
        if (!$this->relatedParent) {
            return false;
        }
        $flag = false;
        foreach ($this->items as $item) {
            if (isset($this->changedKeys[$item->key])) {
                $item->object_id = $this->relatedParent->id;
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
