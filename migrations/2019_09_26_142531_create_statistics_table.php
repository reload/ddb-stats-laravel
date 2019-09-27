<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace
class CreateStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->timestamp('timestamp', 6);
            $table->string('guid');
            $table->string('event');
            $table->string('object_id');
            $table->string('item_id');
            $table->json('details');
            $table->unique(['guid', 'list', 'material']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('statistics');
    }
}
