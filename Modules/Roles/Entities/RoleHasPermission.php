<?php

namespace Modules\Roles\Entities; 

use Illuminate\Database\Eloquent\Model;

class RoleHasPermission extends Model
{
    /**
     * The table associated with the model.
     * @var string $table
     */
    protected $table = 'role_has_permissions';

    /**
     * The attributes that are mass assignable. That means these attributes can be
     * passed to class constructor as array and attributes will be set automatically.
     * @var array $fillable
     */
    protected $fillable = ['permission_id', 'role_id'];
}
