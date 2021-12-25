<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZakatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zakats', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no');

            $table->unsignedBigInteger('receive_from');
            $table->foreign('receive_from')->references('id')->on('users');

            $table->unsignedBigInteger('zakat_pic')->nullable();
            $table->foreign('zakat_pic')->references('id')->on('users');

            $table->date('transaction_date');
            $table->string('hijri_year');
            $table->string('family_head');
            $table->decimal('total_rp', 13, 2);
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
        Schema::table('zakats', function (Blueprint $table) {
            $table->dropForeign(['receive_from']);
            $table->dropForeign(['zakat_pic']);
        });
        Schema::dropIfExists('zakats');
    }
}
