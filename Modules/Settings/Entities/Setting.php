<?php

namespace Modules\Settings\Entities;

use App\Models\User;
use App\Models\Imageable;
use App\Traits\CreatedUpdatedBy;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class Setting extends Model implements TranslatableContract
{
    use Translatable, SoftDeletes, CreatedUpdatedBy;

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
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo
     */
    public function child(): BelongsTo
    {
        return $this->belongsTo(Setting::class, 'parent_id');
    }

    /**
     * @return HasMany
     */
    public function childs(): HasMany
    {
        return $this->hasMany(Setting::class, 'parent_id');
    }

    public static function getParentIdBySlug($slug)
    {
        return self::where('slug', $slug)->pluck('id') ?? NULL;
    }

    public static function getSettingDataBySlug($slug)
    {
        return self::where('parent_id', self::getParentIdBySlug($slug))
                    ->whereNULL('deleted_at')
                    ->whereStatus(true)
                    ->get();
    }

}
