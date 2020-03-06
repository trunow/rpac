<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('subject')->comment('Who, e.g. admin or author');
            $table->string('action')->comment('Action, e.g. view or edit');
            $table->string('object')->comment('What, e.g. post or article');
            $table->timestamps();

            $table->unique(['entity', 'role', 'action']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('permissions');
    }
}
