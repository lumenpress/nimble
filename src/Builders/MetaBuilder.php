<?php

namespace LumenPress\Nimble\Builders;

class MetaBuilder extends Builder
{
    public function objectKey($objectKey)
    {
        return $this->where($this->getModel()->getObjectKeyName(), $objectKey);
    }
}
