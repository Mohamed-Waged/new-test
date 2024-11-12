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
        Schema::create('lecturers', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('app_id')->unique()->nullable();
            $table->string('slug')->index()->nullable();
            $table->decimal('total_amount', 12,2)->default(false);
            $table->decimal('deserved_amount', 12,2)->default(false);

            $table->integer('sort')->default(false);
            $table->boolean('is_active')->default(false);
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('lecturer_translations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lecturer_id')->unsigned()->nullable();

            $table->string('locale')->index();
            $table->string('name')->nullable();
            $table->string('position')->nullable();
            $table->string('university')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();

            $table->unique(['lecturer_id', 'locale']);
            $table->foreign('lecturer_id')->references('id')->on('lecturers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lecturer_translations');
        Schema::dropIfExists('lecturers');
    }
};
