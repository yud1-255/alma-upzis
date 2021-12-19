<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMuzakkisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('muzakkis', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->boolean('is_bpi');
            $table->string('bpi_block_no');
            $table->string('bpi_house_no');
            $table->timestamps();
        });

        Schema::table('zakat_lines', function (Blueprint $table) {
            $table->unsignedBigInteger('muzakki_id')->after('zakat_id');
            $table->foreign('muzakki_id')->references('id')->on('muzakkis');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('zakat_lines', function (Blueprint $table) {
            $table->dropForeign(['muzakki_id']);
            $table->dropColumn('muzakki_id');
        });

        Schema::dropIfExists('muzakkis');
    }
}
