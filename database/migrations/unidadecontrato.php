<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        Schema::create('unidadecontrato', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->text('ucidentificacao')->nullable()->default(null);
            //$table->bigInteger('fkidunidade')->default(0);
            $table->unsignedBigInteger('fkidunidade');
            $table->foreign('fkidunidade')->references('id')->on('unidade')->constrained('uniade')->cascadeOnUpdate()->restrictOnDelete();
            $table->bigInteger('fkidtipoarq')->default(0);
            $table->longText('uctexto')->nullable()->default(null);
            $table->binary('ucarq')->nullable()->default(null);
            $table->dateTime('ucdatainiciovigencia')->nullable()->default(null);
            $table->dateTime('ucdatafinavigencia')->nullable()->default(null);
            $table->dateTime('ucdatacadastro')->nullable()->default(null);
            $table->string('ucnumerocontrato', 50)->default('');
            $table->string('ucextensao', 50)->default('');
            $table->text('ucmyme')->nullable()->default(null);
            $table->text('ucnomearquivo')->nullable()->default(null);
            $table->integer('uctamanho')->default(0);
            $table->integer('ucsavetype')->nullable()->default(null); //1=salva na pasta 2=salva no banco 3=salva no banco e na pasta
            $table->text('ucpath')->nullable()->default(null);
            $table->unsignedBigInteger('fkidgestor');
            $table->foreign('fkidgestor')->references('id')->on('gestor')->constrained('gestor')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->integer('flagexibe')->default(0);
            $table->dateTime('ucversao')->nullable()->default(null);
            $table->index(['fkidtipoarq', 'fkidunidade']);
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
        Schema::dropIfExists('unidadecontrato');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
