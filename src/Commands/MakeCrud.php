<?php

namespace Sanjab\Commands;

use Sanjab\Sanjab;
use Illuminate\Console\GeneratorCommand;

/**
 * Make a CRUD controller command.
 */
class MakeCrud extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sanjab:make:crud {name}'.
                           '{--m|model= : Model for crud}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new sanjab CRUD controller';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'CrudController';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/crud.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers\Admin\Crud';
    }

    /**
     * @see parent::buildClass
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);
        $className = str_ireplace('controller', '', class_basename($name));
        if (empty($this->option('model'))) {
            $stub = str_replace('DummyModel', '\\'.$this->rootNamespace().$className, $stub);
        } else {
            $stub = str_replace('DummyModel', '\\'.$this->rootNamespace().$this->option('model'), $stub);
        }
        $stub = str_replace('DummyRoute', mb_strtolower(str_plural($className)), $stub);
        $stub = str_replace('DummyTitles', str_plural($className), $stub);
        $stub = str_replace('DummyTitle', $className, $stub);

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
