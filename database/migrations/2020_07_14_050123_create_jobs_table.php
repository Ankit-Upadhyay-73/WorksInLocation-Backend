<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) 
        {
            $table->bigInteger('id');
            $table->string('contactNo');
            $table->string('jobName');
            $table->string('city');
            $table->string('district');
            $table->string('state');
            $table->string('location');
            $table->bigInteger('noOfWorkers');
            $table->string('jobOnDate');
            $table->string('others')->nullable();
            $table->string('isClosed')->nullable();
            $table->timestamps();
        });

        Schema::table('jobs',function(Blueprint $table)
        {
            $table->dropColumn(['city','state','district']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jobs');
    }
}
