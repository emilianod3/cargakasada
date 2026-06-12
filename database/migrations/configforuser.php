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
        Schema::create('configforuser', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('identificacao', 250)->nullable()->default(null);
            $table->text('exemplo')->nullable()->default(null);
            $table->integer('tipodado')->default(0)->comment('0=nao Padronizado 1=Afeta User 2=Afeta Permissao');
            $table->integer('status')->default(1);
            $table->integer('classificacao')->default(0)->comment('1=Comportamento do Sistema/Usuário 2=Permissão Especial 3=Configuração do Usuário 0=Não Classificado');
            $table->string('valor1', 250)->default('');
            $table->string('valor2', 250)->default('');
            $table->integer('flagdelete')->default(0);
            $table->integer('flagexibe')->default(1);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(1);
            $table->integer('flagcontrole')->default(0);
            $table->dateTime('versao')->nullable()->default(null);
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
        Schema::dropIfExists('configforuser');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');  
    }
};

/**
 * tipodado = 1=Afeta Usuário 2=Afeta Permissão Usuário 0=Não Padronizado
 * classificacao = 1=Comportamento do Sistema/Usuário 2=Permissão Especial 3=Configuração do Usuário 0=Não Classificado
 */



