<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Brokenice\LaravelMysqlPartition\Models\Partition;
use Brokenice\LaravelMysqlPartition\Schema\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('status_id')->index();
            $table->string('title');
            $table->boolean('main')->index();
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('NO ACTION');


            $table->foreign('status_id')
                ->references('id')
                ->on('status')
                ->onDelete('NO ACTION');
        });
        // Force autoincrement of one field in composite primary key
        Schema::forceAutoIncrement('jobs', 'id');

        Schema::partitionByRange('jobs', 'status',
            [
                new Partition('status0', Partition::RANGE_TYPE, 0),
                new Partition('status1', Partition::RANGE_TYPE, 1),
                new Partition('status2', Partition::RANGE_TYPE, 2)
            ]
        );
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
