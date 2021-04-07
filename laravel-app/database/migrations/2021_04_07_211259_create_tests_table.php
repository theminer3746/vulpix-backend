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
            $table->string('applicationId');
            $table->string('androidVersion');
            $table->string('applicationVersion');
            $table->boolean('forced')->default(false);
            $table->string('dynamicAssignedTo');
            $table->dateTime('dynamicAssignedAt');
            $table->dateTime('dynamicDoneAt');
            $table->string('status')->default('available');
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
