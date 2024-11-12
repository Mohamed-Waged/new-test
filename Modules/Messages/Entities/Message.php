<?php

namespace Modules\Messages\Entities;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Nurseries\Entities\Nursery;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    protected $fillable = [];

    public function user() 
    {
        return $this->belongsTo(User::class, 'user_id')->select('id', 'name');
    }

    public function nursery() 
    {
        return $this->belongsTo(Nursery::class, 'nursery_id');
    }

    public function branch() 
    {
        return $this->belongsTo(Nursery::class, 'branch_id');
    }
}
