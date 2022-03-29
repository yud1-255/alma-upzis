<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddKkChecksumToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('kk_check_count')->after('family_id')->default(0);
        });

        Schema::table('muzakkis', function (Blueprint $table) {
            $table->boolean('use_family_address')->after('bpi_house_no')->default(true);
        });

        Schema::table('zakats', function (Blueprint $table) {
            $table->boolean('is_active')->after('total_transfer_rp')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('kk_check_count');
        });

        Schema::table('muzakkis', function (Blueprint $table) {
            $table->dropColumn('use_family_address');
        });

        Schema::table('zakats', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
}
