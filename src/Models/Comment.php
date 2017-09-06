<?php

namespace Lumenpress\ORM\Models;

class Comment extends Model
{
    const CREATED_AT = 'comment_date';

    const UPDATED_AT = null;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'comments';

    /**
     * The primary key of the model.
     *
     * @var string
     */
    protected $primaryKey = 'comment_ID';

    /**
     * [$dates description].
     *
     * @var array
     */
    protected $dates = ['comment_date'];
}
