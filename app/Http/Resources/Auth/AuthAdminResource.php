<?php

namespace App\Http\Resources\Auth;

use App\Models\Imageable;
use Modules\Roles\Entities\Permission;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthAdminResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @param array $request
     * @return array
     * @author Mahmoud Ahmed <mahmoud.ahmed@gatetechs.com>
     */
    public function toArray($request): array
    {
        // image
        $imageUrl   = $this->image['url'] ?? NULL;
        $image      = Imageable::getImagePath('users', $imageUrl);

        return [
            'userData' => [
                'id'            => $this->id,
                'encryptId'     => encrypt($this->id),
                'fullName'      => $this->name,
                'email'         => $this->email,
                'avatar'        => $image,
                'role'          => auth('api')->user()->roles()->first()->name,
            ],

            'accessToken'       => $this->token,
            'userAbilities'     => $this->userAbilities($this)
        ];
    }

    public function userAbilities($user)
    {
        $userAbilities = [];

        if ($user->roles()->first()->name == 'admin' || $user->roles()->first()->name == 'root') {
            $userAbilities[] = ['action' => 'manage', 'subject' => 'all'];
        } else {
            $permissions = Permission::getPermissionsByRoleId($user->roles()->first()->id);
            $userAbilities[] = ['action' => 'read', 'subject' => 'dashboard'];

            foreach ($permissions as $permission) {
                $userAbilities[] = ['action' => 'read', 'subject' => $permission];
            }
        }

        return $userAbilities;
    }
}
