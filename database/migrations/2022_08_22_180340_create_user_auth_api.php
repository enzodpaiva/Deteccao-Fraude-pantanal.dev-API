<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Jenssegers\Mongodb\Schema\Blueprint;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::collection('userAuthApi', function (Blueprint $_collection) {

            $_collection->unique('username');

            DB::table('userAuthApi')->insert(
                array(
                    'username' => 'landing_page',
                    'password' => '',
                    'apiToken' => '8c62d0a5e116e010c693ba3a34d3f529',
                )
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::collection('userAuthApi', function (Blueprint $_collection) {
            $_collection->drop();
        });
    }
};
