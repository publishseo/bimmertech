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
        Schema::create('software_versions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('system_version');
            $table->string('system_version_alt');
            $table->text('link')->nullable();
            $table->text('st')->nullable();
            $table->text('gd')->nullable();
            $table->boolean('latest')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software_versions');
    }
};
