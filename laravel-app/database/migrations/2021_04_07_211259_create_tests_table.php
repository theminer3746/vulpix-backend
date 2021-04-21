<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('application_id');
            $table->string('android_version');
            $table->string('application_version')->nullable();
            $table->boolean('forced')->default(false);
            $table->string('dynamic_assigned_to')->nullable();
            $table->dateTime('dynamic_assigned_at')->nullable();
            $table->dateTime('dynamic_done_at')->nullable();
            $table->string('status')->default('available');
            $table->json('result')->nullable();
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
        Schema::dropIfExists('tests');
    }
}
