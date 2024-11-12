<?php

namespace Modules\Coupons\Entities;

use Illuminate\Database\Eloquent\Model;

class CouponableCode extends Model
{
    /**
     * The attributes that are mass assignable. That means these attributes can be
     * passed to class constructor as array and attributes will be set automatically.
     * @var array $fillable
     */
    protected $fillable = [];
}
