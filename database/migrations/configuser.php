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
        Schema::create('configuser', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            //$table->bigInteger('fkidcal')->default(0); //0=aplica em todo sistema se > 0 e no id da cal escolhida

            //$table->unsignedBigInteger('fkidcal');
            //$table->foreign('fkidcal')->references('id')->on('cal')->constrained('cal')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('fkidcal')->nullable()->constrained('cal')->cascadeOnUpdate()->restrictOnDelete()->default(null);
            $table->unsignedBigInteger('fkidusuario');
            $table->foreign('fkidusuario')->references('id')->on('usuario')->constrained('usuario')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('fkidconfigforuser');
            $table->foreign('fkidconfigforuser')->references('id')->on('configforuser')->constrained('configforuser')->cascadeOnUpdate()->restrictOnDelete();

            //$table->bigInteger('fkidusuario')->default(0);
            //$table->bigInteger('fkidconfigforuser')->default(0);
            $table->integer('status')->default(0);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagexibe')->default(1);
            $table->integer('flagatualiza')->default(1);
            $table->dateTime('versao')->nullable()->default(null);
            $table->index(['fkidcal','fkidusuario','fkidconfigforuser']);
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
        Schema::dropIfExists('configuser');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');  
    }
};
