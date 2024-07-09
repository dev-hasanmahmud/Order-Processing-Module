<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRelationshipFieldsToCommentsTable extends Migration
{
    public function up()
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->unsignedInteger('order_application_id');
            $table->foreign('order_application_id', 'order_application_fk_1721043')->references('id')->on('order_applications');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id', 'user_fk_1721044')->references('id')->on('users');
        });
    }
}
