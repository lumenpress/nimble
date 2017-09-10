<?php

namespace LumenPress\Nimble\Models;

class TermRelationships extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'term_relationships';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['object_id', 'term_taxonomy_id'];

    public $timestamps = false;
}
