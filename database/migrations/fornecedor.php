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
        Schema::create('fornecedor', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fidentificacao', 250)->nullable()->default(null);
            $table->string('fcnpj', 50)->default('');
            $table->string('frazaosocial', 250)->default('');
            $table->string('femail', 250)->default('');
            $table->string('ftelefone', 250)->default('');
            $table->string('fcelular', 250)->default('');
            $table->string('fcep', 10)->default('');
            $table->bigInteger('fkidcidade')->default(0);
            $table->text('fendereco')->default('');
            $table->string('fbairro', 250)->default('');
            $table->string('fnumero', 250)->default('');
            $table->text('fcomplemento')->default('');
            $table->text('fobs')->default('');
            //$table->bigInteger('fkidgestor')->default(0);
            $table->unsignedBigInteger('fkidgestor');
            $table->foreign('fkidgestor')->references('id')->on('gestor')->constrained('gestor')->cascadeOnUpdate()->restrictOnDelete();
            $table->bigInteger('fkidunidade')->default(0);
            $table->dateTime('fdatacadastro')->nullable()->default(null);
            $table->integer('flagexibe')->default(1);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(1);
            $table->dateTime('fversao')->nullable()->default(null);
            $table->index(['fkidgestor','fkidunidade','fkidcidade']);

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
        Schema::dropIfExists('fornecedor');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
