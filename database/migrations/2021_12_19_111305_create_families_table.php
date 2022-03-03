<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFamiliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('families', function (Blueprint $table) {
            $table->id();
            $table->string('head_of_family');
            $table->string('phone');
            $table->string('kk_number');
            $table->string('address');
            $table->boolean('is_bpi');
            $table->string('bpi_block_no')->nullable();
            $table->string('bpi_house_no')->nullable();
            $table->timestamps();
        });

        Schema::table('muzakkis', function (Blueprint $table) {
            $table->unsignedBigInteger('family_id')->after('id');
            $table->foreign('family_id')->references('id')->on('families');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('family_id')->after('remember_token')->nullable();
            $table->foreign('family_id')->references('id')->on('families');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('muzakkis', function (Blueprint $table) {
            $table->dropForeign(['family_id']);
            $table->dropColumn('family_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['family_id']);
            $table->dropColumn('family_id');
        });

        Schema::dropIfExists('families');
    }
}
