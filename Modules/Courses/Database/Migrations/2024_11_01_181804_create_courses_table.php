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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lecturer_id')->unsigned()->nullable();
            $table->bigInteger('course_type_id')->unsigned()->nullable();

            $table->string('slug')->index()->nullable();
            $table->string('intro_link')->nullable();
            $table->string('date')->nullable();
            $table->string('time')->nullable();
            $table->string('duration_in_minutes')->nullable();
            $table->decimal('price', 12,2)->default(false);

            $table->boolean('has_mynursery_certificate')->default(false);
            $table->boolean('has_international_certificate')->default(false);

            $table->integer('sort')->default(false);
            $table->boolean('is_active')->default(false);
            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lecturer_id')->references('id')->on('lecturers')->onDelete('cascade');
            $table->foreign('course_type_id')->references('id')->on('settings')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('course_translations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_id')->unsigned()->nullable();

            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->longText('body')->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'locale']);
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });

        Schema::create('course_videos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_id')->unsigned()->nullable();
            $table->string('course_link')->nullable();
            $table->timestamps();

            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
        });

        Schema::create('course_video_translations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_video_id')->unsigned()->nullable();

            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->longText('body')->nullable();
            $table->timestamps();

            $table->unique(['course_video_id', 'locale']);
            $table->foreign('course_video_id')->references('id')->on('course_videos')->onDelete('cascade');
        });

        Schema::create('course_video_views', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_video_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->string('pause_at')->nullable();
            $table->boolean('is_viewed')->default(false);
            $table->timestamps();

            $table->unique(['course_video_id', 'user_id']);
            $table->foreign('course_video_id')->references('id')->on('course_videos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('course_favorites', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('course_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();
            $table->timestamps();

            $table->unique(['course_id', 'user_id']);
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('course_purchases', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lecturer_id')->unsigned()->nullable();
            $table->bigInteger('course_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();

            $table->decimal('price', 12,2)->default(false);
            $table->decimal('discount', 12,2)->default(false);
            $table->decimal('coupon_value', 12,2)->default(false);
            $table->string('coupon_code', 12,2)->nullable();
            $table->decimal('total_price', 12,2)->default(false);

            $table->string('mynursery_certificate_path')->nullable();
            $table->string('international_certificate_path')->nullable();

            $table->string('invoice_key')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('ref_id')->nullable();
            $table->string('track_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable();

            $table->longText('note')->nullable();
            $table->longText('reply')->nullable();

            $table->boolean('is_confirmed')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lecturer_id')->references('id')->on('lecturers')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_video_translations');
        Schema::dropIfExists('course_video_views');
        Schema::dropIfExists('course_videos');
        Schema::dropIfExists('course_favorites');
        Schema::dropIfExists('course_purchases');
        Schema::dropIfExists('course_translations');
        Schema::dropIfExists('courses');
    }
};
