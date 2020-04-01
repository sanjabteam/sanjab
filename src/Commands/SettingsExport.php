<?php

namespace Sanjab\Commands;

use Sanjab\Models\Setting;
use Illuminate\Console\Command;

/**
 * Export setting as code for seeder.
 */
class SettingsExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sanjab:settings:export
                            {--K|key= : Key of settings group}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make seeder from settings table.';

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
        $key = $this->option('key');
        $output = "\n";

        $settings = null;
        if ($key) {
            $settings = Setting::where('key', $key)->get();
        } else {
            $settings = Setting::orderBy('key')->get();
        }

        foreach ($settings->groupBy('key') as $group => $settingsGroup) {
            if ($key == null) {
                $output .= "\n/* ".str_pad(' '.$group.' ', 74, '-', STR_PAD_BOTH)." */\n";
            }
            foreach ($settingsGroup as $setting) {
                if ($setting->translation) {
                    foreach ($setting->translations as $settingTranslation) {
                        $output .= 'set_setting(\''.$setting->key.'.'.$setting->name.'\', '.var_export($settingTranslation->translated_value, true).", '".$settingTranslation->locale."');\n";
                    }
                } else {
                    $output .= 'set_setting(\''.$setting->key.'.'.$setting->name.'\', '.var_export($setting->value, true).");\n";
                }
            }
        }
        $output = str_replace('NULL', 'null', $output);
        $this->line($output);
    }
}
