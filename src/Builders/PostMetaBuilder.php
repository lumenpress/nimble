<?php 

namespace Lumenpress\ORM\Builders;

class PostMetaBuilder extends MetaBuilder
{
    public function whereKeyValue($key, $value)
    {
        if (is_array($key)) {
            call_user_func_array([$this, 'where'], array_merge(['meta_key'], $key));
        } else {
            $this->where('meta_key', $key);
        }
        if (is_array($value)) {
            call_user_func_array([$this, 'where'], array_merge(['meta_value'], $value));
        } elseif (!is_null($value)) {
            $this->where('meta_value', $value);
        }
        return $this;
    }
}
