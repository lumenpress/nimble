<?php

namespace LumenPress\Nimble\Concerns;

trait TrySerialize
{
    public function trySerialize($value)
    {
        if (! is_string($value)) {
            return $value;
        }

        if ($value === 'b:0;') {
            return false;
        }

        if (($result = @unserialize($value)) !== false) {
            return $result;
        }

        return $value;
    }
}
