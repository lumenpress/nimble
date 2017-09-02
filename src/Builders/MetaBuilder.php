<?php 

namespace Lumenpress\ORM\Builders;

class MetaBuilder extends Builder
{
    public function objectKey($objectKey)
    {
        return $this->where($this->getModel()->getObjectKeyName(), $objectKey);
    }
}
