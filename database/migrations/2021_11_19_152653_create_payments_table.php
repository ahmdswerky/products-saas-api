<?php

use App\Enums\PaymentStatus;
use App\Models\User;
use App\Models\Product;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('public_id')->unique();
            $table->unsignedBigInteger('usd_amount')->index();
            $table->unsignedBigInteger('amount')->index();
            $table->string('currency')->default(config('app.currency'))->index();
            $table->enum('status', PaymentStatus::constants())->default(PaymentStatus::PENDING);
            $table->foreignIdFor(Product::class);
            $table->foreignIdFor(PaymentMethod::class);
            $table->foreignIdFor(User::class);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
