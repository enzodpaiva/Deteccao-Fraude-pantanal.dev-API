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
                    'username' => 'user_authenticate',
                    'password' => '',
                    'apiToken' => 'd796b7986144f8123eb4b95f7ce972f2',
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
