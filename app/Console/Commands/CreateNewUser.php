<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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

       DB::transactions(function () use($user, $role)
       {

        $newUser= User::create($user);
        $newUser->roles()->attach($role->id);
       });

       return 0;
    }
}
