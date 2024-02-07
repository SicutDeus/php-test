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
            $table->boolean('has_foreign_chagned')->default(false);
            $table->unsignedBigInteger('original_id')->nullable()->index();

            $table->timestamps();
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
