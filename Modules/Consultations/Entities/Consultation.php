<?php

namespace Modules\Consultations\Entities;

use App\Models\User;
use App\Models\Imageable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Consultation extends Model
{
    /**
     * The attributes that are mass assignable. That means these attributes can be
     * passed to class constructor as array and attributes will be set automatically.
     * @var array $fillable
     */
    protected $fillable = [];

    /**
     * @return MorphOne
     */
    public function image(): MorphOne
    {
        return $this->morphOne(Imageable::class, 'imageable')->where('type', 0)->select('url');
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
