<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAccessLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('access_logs', function (Blueprint $table) {
			$table->id();

			$table->ipAddress('ip')->nullable();
			$table->unsignedBigInteger('id_user')->nullable()->index();
			$table->string('path', 255);
			$table->string('method', 10);
			$table->integer('status')->nullable();

			$table->json('request_log')->nullable();
			$table->json('response_log')->nullable();
			$table->json('before_log')->nullable();

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
        Schema::dropIfExists('access_logs');
    }
}
