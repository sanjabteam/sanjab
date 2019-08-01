<?php

namespace Sanjab\Commands;

use Sanjab\Sanjab;
use Illuminate\Console\Command;

/**
 * Clear unused files command.
 */
class Clear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sanjab:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear any unused files.';

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
        $this->info('Deleting unused uploaded files by uppy.');
        Sanjab::clearUploadCache();
        $this->info('Unused uploaded files deleted.');
    }
}
