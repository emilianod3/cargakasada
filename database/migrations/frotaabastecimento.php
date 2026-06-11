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
        Schema::create('frotaabastecimento', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->foreignId('fkidfrotaveiculo')->nullable()->constrained('frotaveiculo')->cascadeOnUpdate()->nullOnDelete()->default(0);
            $table->integer('falitros')->default(0);
            $table->date('fadata')->nullable()->default(null);
            $table->double('favalorporlitro', $precision = 8, $scale = 0)->default(0);
            $table->double('favalortotal', $precision = 8, $scale = 0)->default(0);
            $table->double('famediaultimoabastecimento', $precision = 8, $scale = 0)->default(0);
            $table->integer('fakm')->default(0);
            $table->integer('facombustivel')->default(0);
            $table->integer('fastatus')->nullable()->default(1);
            //$table->bigInteger('fkidgestor')->nullable()->default(0);
            $table->unsignedBigInteger('fkidgestor');
            $table->foreign('fkidgestor')->references('id')->on('gestor')->constrained('gestor')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('faobs')->nullable()->default(null);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(1);
            $table->dateTime('vaversao')->nullable()->default(null);
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
        Schema::dropIfExists('frotaabastecimento');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
