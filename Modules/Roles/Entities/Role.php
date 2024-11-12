<?php

namespace Modules\Roles\Entities;

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Imageable;
use Modules\Roles\Entities\ModelHasRole;
use Modules\Roles\Entities\RoleHasPermission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    /**
     * The table associated with the model.
     * @var string $table
     */
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable. That means these attributes can be
     * passed to class constructor as array and attributes will be set automatically.
     * @var array $fillable
     */
    protected $fillable = ['name', 'guard_name', 'status'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return HasMany
     */
    public function modelHasRoles(): HasMany
    {
        return $this->hasMany(ModelHasRole::class, 'role_id');
    }

    /**
     * @return HasMany
     */
    public function roleHasPermissions(): HasMany
    {
        return $this->hasMany(RoleHasPermission::class, 'role_id');
    }

    /**
     * @param int $roleId
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public static function getUsersInRole($roleId): array
    {
        $data = [];
        $rows = DB::table('model_has_roles')
                                ->where('role_id', $roleId)
                                ->where('model_type', 'App\Models\User')
                                ->leftjoin('users as u', 'model_id', 'u.id')
                                ->whereNULL('u.deleted_at')
                                ->get();

        foreach ($rows as $row) {
            $user           = User::where('id', $row->model_id)->whereNULL('deleted_at')->first();
            $imageUrl       = $user->image['url'] ?? NULL;
            $image          = Imageable::getImagePath('users', $imageUrl);

            $data[] = [
                'id'        => $row->model_id,
                'image'     => $image,
                'name'      => $user->name ?? ''
            ];
        }

        return $data;
    }
}
