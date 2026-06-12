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
        Schema::create('funcionarioarq', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->text('faidentificacao');
            $table->unsignedBigInteger('fkidfuncionario');
            $table->foreign('fkidfuncionario')->references('id')->on('funcionario')->constrained('funcionario')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('fkidtipoarq');
            $table->foreign('fkidtipoarq')->references('id')->on('tipoarq')->constrained('tipoarq')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('fames')->default(0);
            $table->integer('faano')->default(0);
            $table->string('faextensao', 50)->default('');
            $table->text('fatitulo');
            $table->longText('fatexto')->nullable()->default(null);
            $table->integer('fatamanho')->default(0);
            $table->binary('faarq')->nullable()->default(null);
            $table->longText('fapath')->nullable()->default(null);
            $table->text('famyme');
            $table->integer('fasavetype')->default(1);  //1=salva na pasta 2=salva no banco 3=salva no banco e na pasta            
            $table->integer('fastatus')->default(1);
            $table->integer('flagexibe')->default(0);
            $table->bigInteger('flaguser')->default(0);
            $table->integer('flagdelete')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->dateTime('faversao')->nullable()->default(null);
            $table->index(['fkidfuncionario', 'fkidtipoarq']);

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
        Schema::dropIfExists('funcionarioarq');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};