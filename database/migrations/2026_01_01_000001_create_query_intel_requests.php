<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('query_intel_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('request_id')->index();
            $table->string('type');
            $table->string('method')->nullable();
            $table->string('uri')->nullable();
            $table->string('controller')->nullable();
            $table->integer('total_queries')->default(0);
            $table->float('total_query_time')->default(0);
            $table->bigInteger('memory_usage')->nullable();
            $table->bigInteger('peak_memory')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('query_intel_requests');
    }
};
