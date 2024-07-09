<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderApplicationsTable extends Migration
{
    public function up()
    {
        Schema::create('order_applications', function (Blueprint $table) {
            $table->increments('id');
            $table->decimal('order_amount', 15, 2);
            $table->longText('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
