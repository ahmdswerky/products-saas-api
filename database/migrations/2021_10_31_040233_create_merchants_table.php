<?php

use App\Models\User;
use App\Models\Organization;
use App\Enums\MerchantStatus;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();
            $table->string('api_key')->unique();
            $table->string('api_secret')->unique();
            $table->string('public_id')->unique();
            $table->string('title');
            //$table->string('reference_id')->nullable();
            //$table->foreignIdFor(PaymentGateway::class);
            $table->foreignIdFor(User::class);
            $table->string('status')->default(MerchantStatus::NONE);
            //$table->boolean('is_able_to_accept_payments')->nullable();
            //$table->boolean('details_submitted')->default(false);
            //$table->string('disabled_reason')->nullable();
            //$table->json('currently_due')->nullable();
            //$table->json('eventually_due')->nullable();
            //$table->unique(['user_id', 'payment_gateway_id']);
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
        Schema::dropIfExists('merchants');
    }
}
