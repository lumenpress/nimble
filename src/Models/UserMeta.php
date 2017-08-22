<?php 

namespace Lumenpress\ORM\Models;

class UserMeta extends Meta
{

    /**
     * [$table description]
     * @var string
     */
    protected $table = 'usermeta';

    /**
     * [$relationKey description]
     * @var string
     */
    protected $objectKey = 'user_id';

}
