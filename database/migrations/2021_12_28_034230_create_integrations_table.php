<?php

use App\Models\IntegrationName;
use App\Models\Merchant;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIntegrationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(IntegrationName::class);
            $table->foreignIdFor(Merchant::class);
            $table->string('key')->nullable();
            $table->string('secret')->nullable();
            $table->boolean('is_used')->default(true);
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
        Schema::dropIfExists('integrations');
    }
}
