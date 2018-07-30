<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('task', function($collection)
        {
            $collection->increments('_id');
            $collection->string('title', 200)->required();
            $collection->string('description', 400);
            $collection->date('due_date')->required();
            $collection->string("completed", 5);
            $collection->date('created_at');
            $collection->date('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('task');
    }
}
