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
        Schema::create('usuario', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('ulogin', 200)->default('');
            $table->text('upassword');
            $table->text('uhash')->default('');
            $table->text('uanotation')->nullable()->default(null);
            $table->dateTime('udatacadastro')->nullable()->default(null);
            $table->dateTime('udatatermosuso')->nullable()->default(null);
            $table->dateTime('udataultimoacesso')->nullable()->default(null);
            $table->integer('ucontadoracesso')->default(1);
            $table->integer('ustatus')->default(1);
            $table->integer('ugestor')->default(0)->comment('0-usuario comum 1-usuario gestor de sistema/contrato');
            $table->bigInteger('fkidunico')->default(0);
            //$table->foreignId('fkidgrupo')->nullable()->constrained('grupo')->cascadeOnUpdate()->nullOnDelete()->default(null);

            $table->unsignedBigInteger('fkidgrupo');
            $table->foreign('fkidgrupo')->references('id')->on('grupo')->constrained('grupo')->cascadeOnUpdate()->restrictOnDelete();


            $table->integer('uaceitetermosuso')->default(0);
            $table->dateTime('udataaceitetermosuso')->nullable()->default(null);
            $table->integer('usolicitalocalizacao')->default(1);
            //$table->bigInteger('fkidgestor')->default(0);
            $table->unsignedBigInteger('fkidgestor');
            $table->foreign('fkidgestor')->references('id')->on('gestor')->constrained('gestor')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->integer('flagcontrole')->default(0);
            $table->dateTime('uversao')->nullable()->default(null);
            $table->index(['fkidunico', 'fkidgestor']);
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
        Schema::dropIfExists('usuario');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

};
