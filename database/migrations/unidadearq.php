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
        Schema::create('unidadearq', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->text('udaidentificacao');
            //$table->bigInteger('fkidunidade')->default(0);
            $table->bigInteger('fkidtipoarq')->default(0);

            $table->unsignedBigInteger('fkidunidade');
            $table->foreign('fkidunidade')->references('id')->on('unidade')->constrained('uniade')->cascadeOnUpdate()->restrictOnDelete();


            $table->string('uadextensao', 50)->default('');
            $table->text('uadmyme');
            $table->longText('uadtexto');
            $table->bigInteger('uadtamanho')->default(0); //Tamanho em Bytes
            $table->binary('uadarq')->nullable()->default(null);
            $table->longText('uapath')->nullable()->default(null);
            $table->integer('uasavetype')->default(1);  //1=salva na pasta 2=salva no banco 3=salva no banco e na pasta
            $table->integer('uadstatus')->default(1);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->integer('flagexibe')->default(0);
            $table->dateTime('uadversao')->nullable()->default(null);
            $table->index(['fkidunidade', 'fkidtipoarq']);
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
        Schema::dropIfExists('unidadearq');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
