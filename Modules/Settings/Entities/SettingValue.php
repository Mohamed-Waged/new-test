<?php

namespace Modules\Settings\Entities;

use App\Models\User;
use Modules\Lecturers\Entities\Lecturer;
use Illuminate\Database\Eloquent\Model;

class SettingValue extends Model
{
    protected $guarded = [];
    //

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lecturer()
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id');
    }
}
