<?php

namespace Modules\Roles\Entities; 

use Illuminate\Database\Eloquent\Model;

class ModelHasRole extends Model
{
    /**
     * The table associated with the model.
     * @var string $table
     */
    protected $table = 'model_has_roles';

    /**
     * The attributes that are mass assignable. That means these attributes can be
     * passed to class constructor as array and attributes will be set automatically.
     * @var array $fillable
     */
    protected $fillable = ['role_id', 'model_type', 'model_id'];
}
