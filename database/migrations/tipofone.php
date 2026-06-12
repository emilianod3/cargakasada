<?php

use App\Http\Controllers\Core\Tools;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


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
        Schema::create('tipo_fone', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('tpfnidentificacao', 180)->default('');  
            $table->unsignedBigInteger('fkidgestor');
            $table->foreign('fkidgestor')->references('id')->on('gestor')->constrained('gestor')->cascadeOnUpdate()->restrictOnDelete();
            //$table->unsignedBigInteger('fkidtabelareferencia');
            //$table->foreign('fkidtabelareferencia')->references('id')->on('tabelareferencia')->constrained('tabelareferencia')->cascadeOnUpdate()->restrictOnDelete();
            //$table->integer('tfstatus')->default(1);
            //$table->date('tfdatacadastro')->nullable()->default(null); 
            //$table->bigInteger('fkidgestor')->default(0);
            $table->dateTime('tpfnversao')->nullable()->default(null);
            $table->integer('flagexibe')->default(1);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(1);
            $table->integer('flagcontrole')->default(0);
            //$table->index(['fkidtabelareferencia']);
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
        Schema::dropIfExists('tipo_fone');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
