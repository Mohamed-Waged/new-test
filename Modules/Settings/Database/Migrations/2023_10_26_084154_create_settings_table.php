<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lecturer_id')->unsigned()->nullable();
            $table->bigInteger('parent_id')->unsigned()->nullable();

            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->longText('value')->nullable();

            $table->integer('sort')->default(false);
            $table->boolean('is_active')->default(false);

            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lecturer_id')->references('id')->on('lecturers')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('setting_translations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('setting_id')->unsigned()->nullable();

            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->longText('body')->nullable();
            $table->timestamps();

            $table->unique(['setting_id', 'locale']);
            $table->foreign('setting_id')->references('id')->on('settings')->onDelete('cascade');
        });

        Schema::create('setting_values', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('setting_id')->unsigned()->nullable();
            $table->bigInteger('type_id')->unsigned()->nullable(); // consultations_types etc...

            $table->string('key')->nullable();
            $table->string('value')->nullable();
            $table->string('type')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->foreign('setting_id')->references('id')->on('settings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting_translations');
        Schema::dropIfExists('setting_values');
        Schema::dropIfExists('settings');
    }
};
