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
        Schema::create('cfgusercal', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->text('ucoptsearch')->nullable()->default(null);
            //$table->foreignId('fkidusuario')->nullable()->constrained('usuario')->cascadeOnUpdate()->nullOnDelete()->default(0);
           // $table->foreignId('fkidcal')->nullable()->constrained('cal')->cascadeOnUpdate()->nullOnDelete()->default(0);

            $table->unsignedBigInteger('fkidcal');
            $table->foreign('fkidcal')->references('id')->on('cal')->constrained('cal')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('fkidusuario');
            $table->foreign('fkidusuario')->references('id')->on('usuario')->constrained('usuario')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('uctipo')->default(0)->comment('1=numero de regs por pagina 2=outras configuracoes');
            $table->integer('ucregporpagina')->default(0);
            $table->integer('ucusefavorito')->default(0);
            $table->integer('ucsavefavorito')->default(0);
            $table->integer('ucusesizecolumnsrelative')->default(0);
            $table->integer('ucmaximizado')->default(0);
            $table->integer('ucsizefixo')->default(0);
            $table->integer('ucsizerelative')->default(0);
            $table->integer('ucwidth')->default(0);
            $table->integer('ucheight')->default(0);
            $table->integer('ucuseteclaatalho')->default(0);
            $table->integer('ucstatus')->default(0);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->dateTime('ucversao')->nullable()->default(null);
            $table->index(['fkidusuario','fkidcal']);
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
        Schema::dropIfExists('cfgusercal');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
