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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lecturer_id')->unsigned()->nullable();
            $table->bigInteger('consultation_type_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();

            $table->string('date')->nullable();
            $table->string('time')->nullable();
            $table->string('period')->nullable();
            $table->string('delayed_date')->nullable();
            $table->string('delayed_time')->nullable();
            $table->boolean('is_user_approved_on_delayed')->default(false);

            $table->decimal('price', 12,2)->default(false);
            $table->decimal('discount', 12,2)->default(false);
            $table->decimal('coupon_value', 12,2)->default(false);
            $table->decimal('total_price', 12,2)->default(false);

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
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_contacted')->default(false);

            $table->timestamps();
            $table->softDeletes();
            $table->timestamp('contacted_at')->nullable();

            $table->foreign('lecturer_id')->references('id')->on('lecturers')->onDelete('cascade');
            $table->foreign('consultation_type_id')->references('id')->on('settings')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('consultation_reviews', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('consultation_id')->unsigned()->nullable();
            $table->string('rate')->index();
            $table->longText('review')->nullable();
            $table->timestamps();

            $table->foreign('consultation_id')->references('id')->on('consultations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consultation_reviews');
        Schema::dropIfExists('consultations');
    }
};
