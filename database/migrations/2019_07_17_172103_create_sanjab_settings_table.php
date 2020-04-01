<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSanjabSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sanjab_settings', function (Blueprint $table) {
            $table->increments('id');
            $table->boolean('translation')->default('0');
            $table->string('key', 100)->nullable();
            $table->string('name', 100);
            $table->longText('value')->nullable();
            $table->timestamps();

            $table->unique(['key', 'name']);
        });
        Schema::create('sanjab_setting_translations', function (Blueprint $table) {
            $table->increments('id');
            $table->longText('translated_value')->nullable();

            $table->unsignedInteger('setting_id');
            $table->string('locale', 10)->index();
            $table->unique(['setting_id', 'locale']);
            $table->foreign('setting_id')->references('id')->on('sanjab_settings')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sanjab_setting_translations');
        Schema::dropIfExists('sanjab_settings');
    }
}
