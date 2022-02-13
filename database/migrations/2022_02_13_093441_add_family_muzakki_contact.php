<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFamilyMuzakkiContact extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('families', function (Blueprint $table) {
            $table->string('bpi_house_no')->after('address')->nullable();
            $table->string('bpi_block_no')->after('address')->nullable();
            $table->boolean('is_bpi')->after('address');
        });

        Schema::table('muzakkis', function (Blueprint $table) {
            $table->string('phone')->after('name')->nullable();
            $table->string('bpi_block_no')->nullable()->change();
            $table->string('bpi_house_no')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('families', function (Blueprint $table) {
            $table->dropColumn('is_bpi');
            $table->dropColumn('bpi_block_no');
            $table->dropColumn('bpi_house_no');
        });

        Schema::table('muzakkis', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->string('bpi_block_no')->nullable(false)->change();
            $table->string('bpi_house_no')->nullable(false)->change();
        });
    }
}
