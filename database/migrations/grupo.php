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
        
        Schema::create('grupo', function (Blueprint $table) {
            //$table->engine = 'InnoDB';
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('gidentificacao')->default('');
            $table->integer('gstatus')->default(1);
            $table->text('ganotation')->nullable()->default(null);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->integer('flagcontrole')->default(0);
            $table->dateTime('gversao')->nullable()->default(null);
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
        Schema::dropIfExists('grupo');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
