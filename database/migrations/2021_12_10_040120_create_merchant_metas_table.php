<?php

use App\Models\Merchant;
use App\Enums\MerchantStatus;
use App\Models\PaymentGateway;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantMetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_metas', function (Blueprint $table) {
            $table->id();
            $table->string('reference_id')->nullable();
            $table->foreignIdFor(Merchant::class);
            $table->foreignIdFor(PaymentGateway::class);
            $table->enum('status', MerchantStatus::constants())->default(MerchantStatus::NONE);
            $table->boolean('is_able_to_accept_payments')->nullable();
            $table->boolean('primary_email_confirmed')->default(false);
            $table->boolean('details_submitted')->default(false);
            $table->string('disabled_reason')->nullable();
            $table->json('currently_due')->nullable();
            $table->json('eventually_due')->nullable();
            $table->unique(['merchant_id', 'payment_gateway_id']);
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
        Schema::dropIfExists('merchant_metas');
    }
}
