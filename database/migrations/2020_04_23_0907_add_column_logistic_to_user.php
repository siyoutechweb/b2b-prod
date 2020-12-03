<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnLogisticToUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('users', function (Blueprint $table) {
            // $table->dropColumn(['logistique_servie','longitude','latitude']);
            $table->boolean('logistic_service')->default(false)->after('product_visibility');
            $table->float('lat')->nullable();
            $table->float('long')->nullable();
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
            $table->dropColumn(['logistic_service','long','lat']);
        });
    }
}
