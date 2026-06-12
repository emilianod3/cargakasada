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
        Schema::create('ramoatividade', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            
            $table->string('rtidentificacao', 250)->nullable()->default(null);
            $table->string('rtcnaeversao', 10)->nullable()->default('');
            $table->string('rtcnaesecao', 10)->nullable()->default('');
            $table->string('rtcnaedivisao', 10)->nullable()->default('');
            $table->string('rtcnaegrupo', 20)->nullable()->default('');
            $table->string('rtcnaeclasse', 20)->nullable()->default('');
            $table->string('rtcnaesubclasse', 50)->nullable()->default('');
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->dateTime('rtversao')->nullable()->default(null);
        
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
        Schema::dropIfExists('ramoatividade');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');  
    }
};
