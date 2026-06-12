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
        Schema::create('columncal', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            //$table->foreignId('fkidcal')->nullable()->constrained('cal')->cascadeOnUpdate()->nullOnDelete()->default(null);
            $table->unsignedBigInteger('fkidcal');
            $table->unsignedBigInteger('fkidgestor');
            $table->foreign('fkidgestor')->references('id')->on('gestor')->constrained('gestor')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('fkidcal')->references('id')->on('cal')->constrained('cal')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('clsize', 50)->default('1');
            $table->integer('clorder')->default(1);
            $table->integer('clvisible')->default(1);
            $table->string('clheader', 250)->default('');
            $table->integer('clstatus')->default(1);
            $table->string('clname', 50)->default('');
            $table->string('clalinhamento', 50)->default('1');
            $table->integer('cltipocampo')->default(0)->comment('Tipo de campo que será tratado');  //tipo padrao = 0 - tipo secundario  do mesmo modulo = 5 - para exibir nos dois = 3
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->dateTime('clversao')->nullable()->default(null);
            $table->index(['fkidcal']);
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
        Schema::dropIfExists('columncal');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
