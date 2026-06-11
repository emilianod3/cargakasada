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
        Schema::create('logatividade', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->integer('fktipoacao')->default(1);
            $table->bigInteger('fkidusuario')->default(1);
            $table->integer('idregistro')->default(1);
            $table->string('llocal')->default('');
            $table->string('lip')->default('');
            $table->string('lagent')->default('');
            $table->string('lgonde')->default('');  
            $table->text('lgtexto')->nullable()->default(null);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->dateTime('lgversao')->nullable()->default(null);     
            $table->index(['fktipoacao', 'fkidusuario']);  
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
        Schema::dropIfExists('logatividade');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
