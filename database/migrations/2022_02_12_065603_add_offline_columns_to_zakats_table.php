<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOfflineColumnsToZakatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('zakats', function (Blueprint $table) {
            $table->string('receive_from_name')->after('family_head');
            $table->boolean('is_offline_submission')->after('receive_from_name');
            $table->decimal('total_transfer_rp', 13, 2)->after('total_rp');
            $table->decimal('unique_number')->after('total_rp');
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
            $table->dropColumn('receive_from_name');
            $table->dropColumn('is_offline_submission');
            $table->dropColumn('unique_number');
            $table->dropColumn('total_transfer_rp');
        });
    }
}
