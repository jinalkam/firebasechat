<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDevicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_devices', function (Blueprint $table) {
            $table->engine = 'InnoDB';
			$table->integer('user_id')->unsigned();
            $table->text('device_id')->unique('device_id',100);
            $table->string('access_token_id', 100);
            $table->string('mobile_number', 20)->nullable();
            $table->boolean('mobile_number_verified')->default(false);
            $table->enum('status', ['0', '1'])->default('1')->comment('0=Inactive Device|1=Active Device')->nullable();
            $table->timestamps();

            // Setting Foreign key constraints.
            $table->foreign('user_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_devices');
    }
}
