<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('history_savings', function (Blueprint $table) {
            $table->id();

            $table->string('change_author')->nullable();
            $table->string('table_name')->index();
            $table->json('changes')->nullable();
            $table->boolean('first_created')->default(false);
            $table->unsignedBigInteger('original_id')->nullable()->index();

            $table->timestamp('change_made_at', 6)->nullable();

            $table->index(['table_name', 'original_id']);

//            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_savings');
    }
};
