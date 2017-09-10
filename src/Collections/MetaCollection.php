<?php

namespace LumenPress\Nimble\Collections;

use Illuminate\Support\Arr;

class MetaCollection extends Collection
{
    /**
     * Determine if an item exists at an offset.
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function offsetExists($key)
    {
        if (is_numeric($key)) {
            return parent::offsetExists($key);
        }

        foreach ($this->items as $index => $item) {
            if ($item->key == $key) {
                return $item->value == '' ? false : true;
            }
        }

        return false;
    }

    /**
     * Get an item at a given offset.
     *
     * @param mixed $key
     *
     * @return mixed
     */
    public function offsetGet($key)
    {
        if (is_numeric($key)) {
            return isset($this->items[$key]) ? $this->items[$key]->value : null;
        }

        foreach ($this->items as $index => $item) {
            if ($item->key == $key) {
                // Returns null when value is empty.
                return $item->value == '' ? null : $item->value;
            }
        }
    }

    /**
     * Set the item at a given offset.
     *
     * @param mixed $key
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($key, $value)
    {
        if (is_null($key) || is_numeric($key)) {
            if (! is_array($value)) {
                throw new \Exception('value invalid');
            }

            if (is_null($key) && ! Arr::has($value, ['key', 'value', 'object_id'])) {
                throw new \Exception('value invalid');
            }

            if (is_null($key)) {
                $key = count($this->items);
                $this->items[$key] = $this->related->newInstance();
            }

            $item = $this->items[$key];

            foreach ($value as $k => $v) {
                $item->$k = $v;
            }

            $this->changedKeys[$item->key] = true;

            return;
        }

        $this->changedKeys[$key] = true;

        foreach ($this->items as $index => $item) {
            if ($item->key == $key) {
                $item->value = $value;

                return;
            }
        }

        $item = $this->related->newInstance();
        $item->key = $key;
        $item->value = $value;
        $this->items[] = $item;
    }

    /**
     * Unset the item at a given offset.
     *
     * @param string $key
     *
     * @return void
     */
    public function offsetUnset($key)
    {
        if (is_numeric($key)) {
            $this->extraItems[] = $this->items[$key];
            unset($this->items[$key]);

            return;
        }
        foreach ($this->items as $index => $item) {
            if ($item->key == $key) {
                $this->extraItems[] = $item;
                unset($this->items[$index]);
            }
        }
    }

    /**
     * [save description].
     *
     * @return [type] [description]
     */
    public function save()
    {
        $flag = false;

        foreach ($this->items as $item) {
            if (isset($this->changedKeys[$item->key])) {
                if ($this->relatedParent) {
                    $item->object_id = $this->relatedParent->getKey();
                }
                if (! $item->object_id) {
                    throw new \Exception('object_id invalid.');
                }
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
