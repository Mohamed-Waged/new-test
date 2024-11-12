<?php

namespace Modules\Coupons\Entities;

use App\Models\User;
use App\Models\Imageable;
use Illuminate\Database\Eloquent\Model;
use Modules\Lecturers\Entities\Lecturer;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Couponable extends Model implements TranslatableContract
{
    use Translatable;

    /**
     * The attributes that are mass assignable. That means these attributes can be
     * passed to class constructor as array and attributes will be set automatically.
     * @var array $fillable
     */
    protected $fillable = [];

    /**
     * The translated attributes associated with the model.
     * @var array $translatedAttributes
     */
    public $translatedAttributes = ['title', 'body'];

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
        return $this->belongsTo(User::class, 'user_id')->select('id', 'name');
    }

    /**
     * @return BelongsTo
     */
    public function lecturer() : BelongsTo
    {
        return $this->belongsTo(Lecturer::class, 'lecturer_id');
    }

    /**
     * @return HasMany
     */
    public function codes(): HasMany
     {
        return $this->hasMany(CouponableCode::class, 'couponable_id');
    }

}
