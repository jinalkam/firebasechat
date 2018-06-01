<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupMembers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
public function up()
    {
        Schema::create('group_members', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('group_id')->unsigned();
            $table->integer('user_id')->unsigned();
			$table->boolean('is_admin')->default(false);
			$table->char('status', 1)->nullable()->default('P')->comment('A = Approved, P = Pending, I = Investigating, R = Rejected, B = Blocked, D = Deleted'); // A = Approved, P = Pending, I = Investigating, R = Rejected, B = Blocked, D = Deleted
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
        Schema::dropIfExists('group_members');
    }
}
