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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lecturer_id')->unsigned()->nullable();
            $table->bigInteger('book_type_id')->unsigned()->nullable();

            $table->string('slug')->unique();
            $table->decimal('price', 12,2)->default(false);
            $table->integer('pages_no')->default(false);
            $table->string('published_at')->nullable();

            $table->integer('sort')->default(false);
            $table->boolean('is_active')->default(false);

            $table->bigInteger('created_by')->unsigned()->nullable();
            $table->bigInteger('updated_by')->unsigned()->nullable();
            $table->bigInteger('deleted_by')->unsigned()->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('lecturer_id')->references('id')->on('lecturers')->onDelete('cascade');
            $table->foreign('book_type_id')->references('id')->on('settings')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('book_translations', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('book_id')->unsigned()->nullable();

            $table->string('locale')->index();
            $table->string('title')->nullable();
            $table->longText('body')->nullable();
            $table->timestamps();

            $table->unique(['book_id', 'locale']);
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
        });

        Schema::create('book_sales', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('book_id')->unsigned()->nullable();
            $table->bigInteger('user_id')->unsigned()->nullable();

            $table->decimal('price', 12,2)->default(false);
            $table->decimal('discount', 12,2)->default(false);
            $table->decimal('coupon_value', 12,2)->default(false);
            $table->string('coupon_code', 12,2)->nullable();
            $table->decimal('total_price', 12,2)->default(false);

            $table->string('invoice_key')->nullable();
            $table->string('payment_id')->nullable();
            $table->string('transaction_id')->nullable();
            $table->string('ref_id')->nullable();
            $table->string('track_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable();

            $table->boolean('is_confirmed')->default(false);
            $table->boolean('is_cancelled')->default(false);
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
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
        Schema::dropIfExists('book_translations');
        Schema::dropIfExists('book_sales');
        Schema::dropIfExists('books');
    }
};
