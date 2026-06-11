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
        Schema::create('cfgsist', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            //$table->string('tipocliente', 50)->default('Empresa');
            //$table->bigInteger('fkidgestor')->nullable()->default(0);
            $table->unsignedBigInteger('fkidgestor');
            $table->foreign('fkidgestor')->references('id')->on('gestor')->constrained('gestor')->cascadeOnUpdate()->restrictOnDelete();$table->unsignedBigInteger('fkidconfig');
            $table->foreign('fkidconfig')->references('id')->on('config')->constrained('config')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('valor1', 250)->default('');
            $table->string('valor2', 250)->default('');
/*			
            $table->integer('saveLog')->nullable()->default(1);
            $table->integer('avisoprazologon')->default(0);
            $table->integer('savearquivomorto')->default(0);
            $table->integer('protocoloautomatico')->default(1);
            $table->integer('numeracaoautomatica')->default(1);
            $table->integer('correspondenciarecebprotocolada')->default(1);
            $table->integer('correspondenciaenviaprotocolada')->default(1);
            $table->string('diassessoesordinarias', 50)->default('Segundas');
            $table->time('horasessoesordinarias')->nullable()->default(null);
            $table->integer('temredacaofinal')->default(1);*/
            $table->integer('transtatus')->default(1);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
			$table->integer('flagcontrole')->default(1);
            $table->dateTime('tranversao')->nullable()->default(null);
            $table->index(['fkidgestor']);
			$table->index(['fkidconfig']);
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
        Schema::dropIfExists('cfgsist');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
