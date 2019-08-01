<?php

namespace Sanjab\Commands;

use Sanjab\Sanjab;
use Illuminate\Console\GeneratorCommand;

/**
 * Make a Dashboard controller command.
 */
class MakeDashboard extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sanjab:make:dashboard';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new sanjab dashboard controller';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'DashboardController';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/dashboard.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers\Admin';
    }

    /**
     * @see parent::handle
     */
    public function handle()
    {
        parent::handle();
        $name = $this->qualifyClass($this->getNameInput());
        Sanjab::addControllerToConfig($name);
        $this->info('Controller added to config successfully.');
    }
}
