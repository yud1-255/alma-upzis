<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateZakatLineTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('zakat_lines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('zakat_id');
            $table->foreign('zakat_id')->references('id')->on('zakats');
            $table->decimal('fitrah_rp', 8, 2);
            $table->decimal('fitrah_kg', 8, 2);
            $table->decimal('fitrah_lt', 8, 2);
            $table->decimal('maal_rp', 8, 2);
            $table->decimal('profesi_rp', 8, 2);
            $table->decimal('infaq_rp', 8, 2);
            $table->decimal('wakaf_rp', 8, 2);
            $table->decimal('fidyah_kg', 8, 2);
            $table->decimal('fidyah_rp', 8, 2);
            $table->decimal('kafarat_rp', 8, 2);
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
        Schema::table('zakat_lines', function (Blueprint $table) {
            $table->dropForeign(['zakat_id']);
        });
        Schema::dropIfExists('zakat_lines');
    }
}
