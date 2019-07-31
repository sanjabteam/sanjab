<?php

namespace Sanjab\Commands;

use Illuminate\Console\Command;
use Silber\Bouncer\Database\Role;
use Bouncer;
use App\User;
use Sanjab\Sanjab;

class ClearUpload extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sanjab:clear_upload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear unused uploaded files by uppy.';

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
