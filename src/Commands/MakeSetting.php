<?php

namespace Sanjab\Commands;

use Sanjab\Sanjab;
use Illuminate\Console\GeneratorCommand;

/**
 * Make a Setting controller command.
 */
class MakeSetting extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'sanjab:make:setting';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new sanjab Setting controller';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'SettingController';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/setting.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers\Admin\Setting';
    }

    /**
     * @see parent::buildClass
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);
        $className = str_ireplace('controller', '', str_ireplace(['SettingController', 'SettingController'], '', class_basename($name)));
        $stub = str_replace('DummyKey', mb_strtolower($className), $stub);
        $stub = str_replace('DummyTitle', $className.' Settings', $stub);

        return $stub;
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
