<?php

namespace Modules\Courses\Entities;

use Illuminate\Database\Eloquent\Model;

class CourseVideoTranslation extends Model
{
    /**
     * The model without timestamps.
     * @var bool $timestamps
     */
    public $timestamps  = false;

    /**
     * The attributes that are mass assignable. That means these attributes can be
     * passed to class constructor as array and attributes will be set automatically.
     * @var array $fillable
     */
    protected $fillable = [];
}
