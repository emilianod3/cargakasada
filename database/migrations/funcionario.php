<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Core\Tools;

return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Padrão de exclusão se a variável de ambiente estiver configurada
        if (env('DATABASE_DELETEONCREATE') == 'SIM') {
            self::down();
        }

        Schema::create('funcionario', function (Blueprint $table) {
            
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->unsignedBigInteger('fkidunico');
            $table->foreign('fkidunico')->references('id')->on('unico')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('fkidunidade');
            $table->foreign('fkidunidade')->references('id')->on('unidade')->cascadeOnUpdate()->restrictOnDelete();
            $table->bigInteger('fkidcargo')->default(0);
            $table->unsignedBigInteger('fkidgestor');
            $table->foreign('fkidgestor')->references('id')->on('gestor')->constrained('gestor')->cascadeOnUpdate()->restrictOnDelete();            
            $table->date('fcdataentrada')->nullable();
            $table->date('fcdatasaida')->nullable();
            $table->dateTime('fcdatacadastro')->nullable()->default(null);
            $table->integer('fcstatus')->default(1);
            $table->string('fcmatricula', 50)->default('');
            $table->string('fcseriecarteira', 50)->default('');
            $table->string('fcnumcarteira', 50)->default('');
            $table->string('fccodigocarteria', 50)->default('');
            $table->date('fcvigenciacarteirainicio')->nullable()->default(null);
            $table->date('fcvigenciacarteirafinal')->nullable()->default(null);
            $table->text('fclinkcarteira')->default('');
            $table->text('fcobs')->default('');
            $table->integer('flagexibe')->default(1);
            $table->integer('flagdelete')->default(0);
            $table->bigInteger('flaguser')->default(0);
            $table->integer('flagatualiza')->default(1);
            $table->dateTime('fcversao')->nullable()->default(null); // Coluna guarded
            $table->index(['fkidunico', 'fkidunidade']);
            $table->index(['fkidcargo']);
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
        // Desativa a checagem de FKs para evitar problemas na exclusão
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('funcionario');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
