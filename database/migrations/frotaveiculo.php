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
        Schema::create('frotaveiculo', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('fvidentificacao', 250)->nullable()->default(null);
            $table->string('fvplaca', 12)->nullable()->default(null);
            $table->string('fvrenavam', 12)->nullable()->default(null);
            $table->string('fvchassi', 50)->nullable()->default(null);
            $table->string('fvfabricacao', 4)->nullable()->default(null);
            $table->string('fvmodelo', 50)->nullable()->default(null);
            $table->text('fvobs')->nullable()->default(null);
            $table->integer('fvstatus')->nullable()->default(1);
            //$table->bigInteger('fkidgestor')->nullable()->default(0);
            $table->unsignedBigInteger('fkidgestor');
            $table->foreign('fkidgestor')->references('id')->on('gestor')->constrained('gestor')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(1);
            $table->dateTime('fvversao')->nullable()->default(null);
            $table->index(['fkidgestor']);
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
        Schema::dropIfExists('frotaveiculo');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
