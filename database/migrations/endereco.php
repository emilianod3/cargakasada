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
        Schema::create('endereco', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->unsignedBigInteger('fkidcidade');
            $table->foreign('fkidcidade')->references('id')->on('cidade')->constrained('cidade')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('endcep', 10)->nullable()->default(null);
            $table->string('endtipologradouro', 50)->nullable()->default(null);
            $table->text('endlogradouro')->nullable()->default(null);
            $table->text('endbairro')->nullable()->default(null);
            $table->unsignedBigInteger('fkidgestor');
            $table->foreign('fkidgestor')->references('id')->on('gestor')->constrained('gestor')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('endstatus')->default(1);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->integer('flagcontrole')->default(0);
            $table->dateTime('endversao')->nullable()->default(null);
            $table->index(['fkidcidade']);
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
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        Schema::dropIfExists('endereco');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');          
    }
};
