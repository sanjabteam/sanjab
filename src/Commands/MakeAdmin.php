<?php

namespace Sanjab\Commands;

use Bouncer;
use Illuminate\Console\Command;
use Silber\Bouncer\Database\Role;

/**
 * Assign super_admin to an user.
 */
class MakeAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sanjab:make:admin
                            {--U|user= : Username of admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make a new super admin';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $username = $this->option('user');
        $user = null;
        if (empty($username)) {
            $username = $this->ask(config('sanjab.login.username'));
        }
        $userModel = config('auth.providers.users.model');
        while (! ($user = $userModel::withoutGlobalScopes()->where(config('sanjab.login.username'), $username)->first())) {
            $this->error('User does not exists!');
            $username = $this->ask(config('sanjab.login.username'));
        };

        if (! Role::where('name', 'super_admin')->exists()) {
            Role::create(['name' => 'super_admin', 'title' => trans('sanjab::sanjab.super_admin')]);
            Bouncer::allow('super_admin')->everything();
        }
        Bouncer::assign('super_admin')->to($user);
    }
}
