<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Core\Tools;

return new class extends Migration
{
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
        Schema::create('unidade', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('undidentificacao', 250)->default('');
            $table->integer('undclienteprincipal')->default(0); 
            $table->string('undtexto', 250)->default('');
            $table->string('unnomefantasia', 250)->default('');
            $table->string('undcep', 250)->default('');
            $table->string('undinscrmunicipal', 250)->default('');
            $table->dateTime('unddatacadastro')->nullable()->default(null); 
            $table->dateTime('unddataasscontrato')->nullable()->default(null);
            $table->dateTime('unddatainicontrato')->nullable()->default(null);
            $table->dateTime('unddatafimcontrato')->nullable()->default(null);
            $table->string('uncnpj', 50)->default('');
            $table->bigInteger('fkidramoatividade')->default(0);
            $table->bigInteger('fkidcidade')->default(0);
            $table->unsignedBigInteger('fkidgestor');
            $table->foreign('fkidgestor')->references('id')->on('gestor')->constrained('gestor')->cascadeOnUpdate()->restrictOnDelete();
            $table->text('undendereco')->nullable()->default(null);
            $table->string('undbairro', 250)->default('');
            $table->string('undnumero', 250)->default('');
            $table->text('undcomplemento')->nullable()->default(null);
            $table->string('unddesignacao', 50)->default('');
            $table->string('undinscrestadual', 50)->default('');
            $table->string('unhorariofuncionamentomanha', 50)->default('');
            $table->string('unhorariofuncionamentotarde', 50)->default('');
            $table->text('undobs')->nullable()->default(null);
            $table->text('unlema')->nullable()->default(null);
            $table->longText('uncomochegar')->nullable()->default(null);
            $table->longText('ungeorreferencia')->nullable()->default(null);
            $table->longText('unhistoria')->nullable()->default(null);
            $table->longText('undadosgeograficos')->nullable()->default(null);
            $table->integer('unsiteativo')->default(1);
            $table->integer('untiposite')->default(1);
            $table->integer('unmodelosite')->default(1);
            $table->dateTime('undversao')->nullable()->default(null); 
            $table->integer('flagexibe')->default(0);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->integer('flagcontrole')->default(0);
            $table->index(['fkidramoatividade', 'fkidcidade','fkidgestor']);

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
        Schema::dropIfExists('unidade');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
