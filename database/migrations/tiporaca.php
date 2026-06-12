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
        Schema::create('tiporaca', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('tridentificacao', 250)->nullable()->default(null);
            $table->unsignedBigInteger('fkidgestor');
            $table->foreign('fkidgestor')->references('id')->on('gestor')->constrained('gestor')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->integer('flagcontrole')->default(0);
            $table->dateTime('trversao')->nullable()->default(null);
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
        Schema::dropIfExists('tiporaca');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');  
    }
};
