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
        Schema::create('config', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('identificacao', 250)->nullable()->default(null);
            $table->text('exemplo')->nullable()->default(null);
            $table->integer('tipodado')->default(0);
            $table->integer('status')->default(1);
            $table->integer('classificacao')->default(0);
            $table->string('valor1', 250)->default('');
            $table->string('valor2', 250)->default('');
            $table->integer('flagdelete')->default(0);
            $table->integer('flagexibe')->default(1);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(1);
            $table->integer('flagcontrole')->default(1);
            $table->dateTime('versao')->nullable()->default(null);
            //$table->index(['fkidgestor']);
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
        Schema::dropIfExists('config');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 
    }
};
