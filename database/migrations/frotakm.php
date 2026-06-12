<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Core\Tools;
return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(ENV('DATABASE_DELETEONCREATE') == 'SIM'){
            self::down();
        }
        Schema::create('frotakm', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->foreignId('fkidfrotaveiculo')->nullable()->constrained('frotaveiculo')->cascadeOnUpdate()->nullOnDelete()->default(0);
            $table->integer('fkinicio')->default(0);
            $table->dateTime('fkdatainicial')->nullable()->default(null);
            $table->integer('fkfinal')->default(0);
            $table->dateTime('fkdatafinal')->nullable()->default(null);
            $table->integer('fkstatus')->nullable()->default(1);
            //$table->bigInteger('fkidgestor')->nullable()->default(0);
            $table->unsignedBigInteger('fkidgestor');
            $table->foreign('fkidgestor')->references('id')->on('gestor')->constrained('gestor')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('fkdestino', 250)->default('');
            $table->text('fkobs')->nullable()->default(null);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(1);
            $table->dateTime('fkversao')->nullable()->default(null);
            $table->index(['fkidfrotaveiculo','fkidgestor']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('frotakm');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
