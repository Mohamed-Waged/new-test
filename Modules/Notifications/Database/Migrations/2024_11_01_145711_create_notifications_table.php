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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lecturer_id')->unsigned()->nullable();
            $table->bigInteger('user_to')->unsigned()->nullable();

            $table->boolean('sent_by_admin')->default(false);
            $table->boolean('is_read')->default(false);

            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->bigInteger('read_at')->unsigned()->nullable();

            $table->foreign('lecturer_id')->references('id')->on('lecturers')->onDelete('cascade');
            $table->foreign('user_to')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('notification_translations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('notification_id')->unsigned()->nullable();

            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->longText('body')->nullable();
            $table->timestamps();

            $table->unique(['notification_id', 'locale']);
            $table->foreign('notification_id')->references('id')->on('notifications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_translations');
        Schema::dropIfExists('notifications');
    }
};
