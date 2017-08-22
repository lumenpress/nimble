<?php 

namespace Lumenpress\ORM\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

class Model extends EloquentModel
{
    public function __toString()
    {
        return '';
    }
}
