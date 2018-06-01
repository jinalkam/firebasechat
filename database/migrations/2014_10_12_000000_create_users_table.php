<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
             $table->engine = 'InnoDB';
            $table->increments('id');
//            $table->string('facebook_id', 100)->unique();
            $table->string('facebook_id', 100)->unique()->nullable();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
  $table->string('name', 50)->change();
            $table->char('gender', 1)->nullable(); // M = Male, F = Female, O = Other
            $table->text('description')->nullable();
            $table->boolean('blur_pics_before_matching')->default(false);
            $table->char('gender_preference', 1)->nullable(); // M = Male, F = Female, A = Any/All
            $table->string('email', 100)->unique()->nullable();
            $table->date('dob')->nullable();
            $table->boolean('facebook_verified')->default(false);
            $table->boolean('linkedin_verified')->default(false);
            $table->boolean('twitter_verified')->default(false);
            $table->boolean('instagram_verified')->default(false);
            $table->char('trust_level', 1); // B = Bronze, S = Silver, G = Gold
            $table->timestamp('last_login')->nullable();
            $table->enum('status', ['P','A'])->default('A')->comment('P=Pending|A=Active');
            $table->boolean('blocked')->default(false);
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
