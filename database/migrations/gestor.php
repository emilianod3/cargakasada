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
        Schema::create('gestor', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('identificacao', 250)->default('');
            $table->string('nomefantasia', 250)->default('');
            $table->text('lema')->nullable();
            $table->date('datacadastro')->nullable()->default(null);
            $table->string('cnpj', 50)->default('');
            //$table->foreignId('fkidcidade')->nullable()->constrained('cidade')->cascadeOnUpdate()->nullOnDelete()->default(null);
            $table->unsignedBigInteger('fkidramoatividade');
            $table->foreign('fkidramoatividade')->references('id')->on('ramoatividade')->constrained('ramoatividade')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('endereco')->nullable();
            $table->string('cep', 9)->nullable()->default(null);
            //$table->foreignId('fkidramoatividade')->nullable()->constrained('ramoatividade')->cascadeOnUpdate()->nullOnDelete()->default(null);
            $table->unsignedBigInteger('fkidcidade');
            $table->foreign('fkidcidade')->references('id')->on('cidade')->constrained('cidade')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('bairro', 250)->default('');
            $table->string('numero', 250)->default('');
            $table->text('complemento')->nullable();
            $table->string('inscrestadual', 50)->default('');
            $table->string('horariofuncionamentomanha', 50)->default('08:00 hs às 12:00 hs');
            $table->string('horariofuncionamentotarde', 50)->default('14:00 hs às 18:00 hs');
            $table->text('obs')->nullable();
            $table->dateTime('versao')->nullable()->default(null);  
            $table->integer('flagexibe')->default(0);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->integer('flagcontrole')->default(0);
            $table->index(['fkidcidade']);
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
        Schema::dropIfExists('gestor');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 
    }
};
