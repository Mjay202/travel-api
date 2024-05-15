<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateNewUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
       $user['name'] = $this->ask('What is the User name?');
       $user['email'] = $this->ask('What is the User email?');
       $user['password'] = $this->secret('What is the User password?');
       
       $roleName = $this->choice('What is the use role?', ['admin', 'editor', 'agent'], 2);
       
       $role= Role::where('name', $roleName)->first();

       if (!$role) { 

        $this->error('Role not found');
        return -1;
        }

        $validator = Validator::make($user, [
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'unique:'.User::class],
            'password' => ['required', Password::defaults()],
        ]);

        if($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return -1;
        }

       DB::transaction(function () use($user, $role)
       {

        $newUser= User::create($user);
        $newUser->roles()->attach($role->id);
       });

       $this->info('User '.$user['name'].' has been succesfully created');
       return 0;
    }
}
