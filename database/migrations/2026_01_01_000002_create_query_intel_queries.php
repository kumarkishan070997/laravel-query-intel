<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('query_intel_queries', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_id')->index();
            $table->text('normalized_sql');
            $table->text('raw_sql');
            $table->json('bindings')->nullable();
            $table->float('execution_time');
            $table->string('connection');
            $table->string('location')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('query_intel_queries');
    }
};
