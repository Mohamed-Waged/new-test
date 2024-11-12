<?php

namespace Database\Seeders;

use Exception;
use App\Models\User;
use Illuminate\Database\Seeder;
use Modules\Roles\Entities\Role;
use Illuminate\Support\Facades\Log;
use Modules\Lecturers\Entities\Lecturer;
use Modules\Roles\Entities\ModelHasRole;

class RootSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            // create root
            if (User::where('name', 'root')->doesntExist()) {
                $root = User::create([
                    'id'            => 1,
                    'ip_address'    => request()->ip(),
                    'name'          => 'root',
                    'email'         => 'root@' . env('APP_NAME') . '.com',
                    'password'      => bcrypt('r00$'),
                    'is_active'     => true
                ]);

                ModelHasRole::insert([
                    'role_id'       => Role::where('name', 'root')->first()->id,
                    'model_type'    => 'App\\Models\\User',
                    'model_id'      => $root->id
                ]);
            }

            // create Super admin
            if (User::where('name', 'Super admin')->doesntExist()) {
                $admin = User::create([
                    'id'            => 2,
                    'ip_address'    => request()->ip(),
                    'name'          => 'Super admin',
                    'email'         => 'admin@' . env('APP_NAME') . '.com',
                    'password'      => bcrypt('admin'),
                    'is_active'     => true
                ]);

                ModelHasRole::insert([
                    'role_id'       => Role::where('name', 'admin')->first()->id,
                    'model_type'    => 'App\\Models\\User',
                    'model_id'      => $admin->id
                ]);
            }

            // create Lecturer Dr.Mostafa Abossaad account
            if (User::where('email', 'lecturer1@site.com')->doesntExist()) {
                $lecturer = User::create([
                    'id'            => 3,
                    'ip_address'    => request()->ip(),
                    'name'          => 'د. مصطفى أبو سعد',
                    'email'         => 'lecturer1@site.com',
                    'password'      => bcrypt('123456$#'),
                    'is_active'     => true
                ]);

                ModelHasRole::insert([
                    'role_id'       => Role::where('name', 'lecturer')->first()->id,
                    'model_type'    => 'App\\Models\\User',
                    'model_id'      => $lecturer->id
                ]);
            }

            // create Lecturer Dr.Mostafa Abossaad Profile
            if (Lecturer::where('slug', 'dr.mostafa_abossaad')->doesntExist()) {
                $row                                    = new Lecturer;
                $row->translateOrNew('en')->name        = 'Dr. Mostafa Abossaad';
                $row->translateOrNew('en')->position    = 'A psychological and educational consultant and one of the pioneers of the Arab world';
                $row->translateOrNew('en')->university  = 'University of Science and Economics';
                $row->translateOrNew('ar')->name        = 'د. مصطفى أبو سعد';
                $row->translateOrNew('ar')->position    = 'استشاري نفسي وتربوي ومن رواد العالم العربي';
                $row->translateOrNew('ar')->university  = 'دكتوراه في علم النفس التربوي';
                $row->user_id                           = User::where('email', 'lecturer1@site.com')->first()->id;
                $row->slug                              = 'dr.mostafa_abossaad';
                $row->app_id                            = 'VYi$upl88HQPy5s02';
                $row->is_active                         = true;
                $row->created_by                        = User::first()->id;
                $row->save();
            }


            // create default user
            if (User::where('name', 'user')->doesntExist()) {
                $user = User::create([
                    'id'            => 4,
                    'ip_address'    => request()->ip(),
                    'name'          => 'user',
                    'email'         => 'user@' . env('APP_NAME') . '.com',
                    'password'      => bcrypt('user'),
                    'is_active'     => true
                ]);

                ModelHasRole::insert([
                    'role_id'       => Role::where('name', 'member')->first()->id,
                    'model_type'    => 'App\\Models\\User',
                    'model_id'      => $user->id
                ]);
            }

        } catch (Exception $e) {
            Log::warning($e);
        }
    }
}
