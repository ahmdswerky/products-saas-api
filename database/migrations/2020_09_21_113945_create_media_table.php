<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->morphs('mediable');
            $table->string('path');
            $table->string('type');
            $table->string('driver')->default(config('filesystems.default'));
            $table->string('group')->nullable();
            $table->string('name')->nullable();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_main')->default(false);
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
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
        Schema::dropIfExists('media');
    }
}
