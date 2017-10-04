<?php

namespace LumenPress\Nimble\Collections;

class OptionCollection extends MetaCollection
{
    public function offsetExists($key)
    {
        return parent::offsetExists($key) ?: $this->related->offsetExists($key);
    }

    public function offsetGet($key)
    {
        return is_null($value = parent::offsetGet($key)) ? $this->related->offsetGet($key) : $value;
    }

    public function save()
    {
        return false;
    }
}
