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
        Schema::create('couponables', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lecturer_id')->unsigned()->nullable();

            $table->bigInteger('couponeable_id')->unsigned()->nullable();
            $table->string('couponeable_type')->nullable();

            $table->integer('coupon_count')->default(false);
            $table->integer('coupon_percentage')->default(false);

            $table->string('slug')->unique();
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

        Schema::create('couponable_translations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('couponable_id')->unsigned()->nullable();

            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->longText('body')->nullable();
            $table->timestamps();

            $table->unique(['couponable_id', 'locale']);
            $table->foreign('couponable_id')->references('id')->on('couponables')->onDelete('cascade');
        });

        Schema::create('couponable_codes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('couponable_id')->unsigned()->nullable();

            $table->string('code')->unique();
            $table->boolean('is_used')->default(false);
            $table->timestamps();

            $table->foreign('couponable_id')->references('id')->on('couponables')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('couponable_codes');
        Schema::dropIfExists('couponable_translations');
        Schema::dropIfExists('couponables');
    }
};