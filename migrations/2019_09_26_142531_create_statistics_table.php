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
            $table->string('guid')->nullable();
            $table->string('event');
            $table->string('collection_id')->nullable();
            $table->string('item_id')->nullable();
            $table->integer('total_count')->nullable();
            $table->json('content');

            $table->index('timestamp');
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
