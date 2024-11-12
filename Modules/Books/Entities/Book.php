<?php

namespace Modules\Books\Entities;

use App\Models\Imageable;
use App\Traits\CreatedUpdatedBy;
use Modules\Settings\Entities\Setting;
use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;

class Book extends Model implements TranslatableContract
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
        return $this->belongsTo(User::class, 'user_id')->select('id', 'name');
    }

    /**
     * @return BelongsTo
     */
    public function bookType(): BelongsTo
    {
        return $this->belongsTo(Setting::class, 'book_type_id');
    }

}
