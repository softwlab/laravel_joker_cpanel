<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPathSegmentToVisitantesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('visitantes', function (Blueprint $table) {
            $table->string('path_segment')->nullable()->after('referrer')
                ->comment('Segmento de URL para templates multipágina');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('visitantes', function (Blueprint $table) {
            $table->dropColumn('path_segment');
        });
    }
}
