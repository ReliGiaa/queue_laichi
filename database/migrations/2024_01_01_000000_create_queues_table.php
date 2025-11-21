<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(){
        Schema::create('queues', function(Blueprint $table){
            $table->id();
            $table->integer('number');
            $table->enum('status',['waiting','served'])->default('waiting');
            $table->timestamps();
        });
    }
    public function down(){ Schema::dropIfExists('queues'); }
};
