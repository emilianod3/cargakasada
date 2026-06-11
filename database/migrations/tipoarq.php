<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\Core\Tools;
use Illuminate\Support\Facades\DB;

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
        Schema::create('tipoarq', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('tpaidentificacao', 250)->nullable()->default(null);
            $table->string('tpaplural', 250)->nullable()->default(null);
            $table->integer('tpaclassificacao')->default(0)->comment('o mesmo id da cal mas ainda não usado'); //o mesmo da cal 0=todos 8-ata  51-unico 1000-galeria 125-noticia 62-conteudoportal
            $table->integer('tpastatus')->default(1);
            $table->bigInteger('fkidgestor')->default(0); 
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->integer('flagexibe')->default(0);
            $table->integer('flagcontrole')->default(0);
            $table->dateTime('tpaversao')->nullable()->default(null);
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
        Schema::dropIfExists('tipoarq');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
