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
        Schema::create('menu', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('mnidentificacao', 250)->default('');
            $table->string('mnicone', 250)->default('');
            $table->string('mndestaque', 250)->default('');
            $table->string('mnskin', 250)->default('');
            $table->string('mnnumeracao', 20)->default('');
            $table->bigInteger('fkidmenunivelacima')->default(0);
            $table->bigInteger('fkidcal')->default(0);
            $table->integer('mnsequencia')->default(1);
            $table->integer('mnstatus')->default(1);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->integer('flagcontrole')->default(0);
            $table->dateTime('mnversao')->nullable()->default(null);
            $table->index(['fkidmenunivelacima', 'fkidcal']);  
             
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
        Schema::dropIfExists('menu');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
