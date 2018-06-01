<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
     public function up()
    {
        Schema::create('groups', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('group_name', 100);
            $table->string('group_icon', 100);
            $table->string('background_image', 100);
            $table->text('group_link');
            $table->text('description');
            $table->string('conversion_id', 100)->unique();
			$table->char('status', 1)->nullable()->default('A')->comment('A = Active, P = Pending, D = Deleted, R = Reported');
            $table->boolean('blocked')->default(false);
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
        Schema::dropIfExists('groups');
    }
}
