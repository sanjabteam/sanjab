<?php

namespace Sanjab\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Sanjab install command.
 */
class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sanjab:install {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install sanjab';

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
        $this->info('Installing Sanjab...');

        $this->line('Publishing configs.');
        $this->call('vendor:publish', ['--provider' => 'Sanjab\SanjabServiceProvider', '--tag' => 'config', '--force' => $this->option('force')]);
        $this->info('Configs published.');

        $this->line('Publishing assets.');
        $this->call('vendor:publish', ['--provider' => 'Sanjab\SanjabServiceProvider', '--tag' => 'assets', '--force' => $this->option('force')]);
        $this->info('Assets published.');

        if (! file_exists(app_path('Http/Controllers/Admin/DashboardController.php')) || $this->option('force')) {
            $this->line('Creating DashboardController.');
            if (file_exists(app_path('Http/Controllers/Admin/DashboardController.php'))) {
                File::delete(app_path('Http/Controllers/Admin/DashboardController.php'));
            }
            $this->call('sanjab:make:dashboard', ['name' => 'DashboardController']);
            $this->info('DashboardController created.');
        }

        if (! file_exists(app_path('Http/Controllers/Admin/Crud/UserController.php')) || $this->option('force')) {
            $this->line('Creating user controller.');
            if (! file_exists(app_path('Http/Controllers/Admin/Crud/UserController.php'))) {
                $this->call('sanjab:make:crud', ['name' => 'UserController']);
            }
            file_put_contents(
                app_path('Http/Controllers/Admin/Crud/UserController.php'),
                file_get_contents(__DIR__.'/stubs/usercontroller.stub')
            );
            $this->info('UserController created.');
        }

        if (file_exists(app_path('User.php'))) {
            $this->line('Adding SanjabUser trait to User Model.');
            $userModelContent = file_get_contents(app_path('User.php'));
            $userModelContent = str_replace('use Notifiable;', 'use Notifiable, SanjabUser;', $userModelContent);
            if (strpos($userModelContent, 'Sanjab\Models\SanjabUser') === false) {
                $userModelContent = str_replace(
                    "use Illuminate\\Foundation\\Auth\\User as Authenticatable;",
                    "use Illuminate\\Foundation\\Auth\\User as Authenticatable;\nuse Sanjab\\Models\\SanjabUser;",
                    $userModelContent
                );
            }
            file_put_contents(app_path('User.php'), $userModelContent);
            $this->line('SanjabUser trait added to User.');
        }

        $this->line('Sanjab installed successfully.');
    }
}
