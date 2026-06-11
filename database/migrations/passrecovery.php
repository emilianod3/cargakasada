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
        Schema::create('passrecovery', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('unidentificacao', 250)->default('');
            //$table->foreignId('fkidusuario')->nullable()->constrained('usuario')->cascadeOnUpdate()->nullOnDelete()->default(null);
            //$table->foreignId('fkidemail')->nullable()->constrained('email')->cascadeOnUpdate()->nullOnDelete()->default(null);
            $table->unsignedBigInteger('fkidemail');
            $table->foreign('fkidemail')->references('id')->on('email')->constrained('email')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('fkidusuario');
            $table->foreign('fkidusuario')->references('id')->on('usuario')->constrained('usuario')->cascadeOnUpdate()->restrictOnDelete();

            $table->string('prip', 30)->nullable()->default('0');
            $table->text('token')->nullable()->default(null);
            $table->dateTime('prdtrecovery')->nullable()->default(null);
            $table->dateTime('prdtregistro')->nullable()->default(null); 
            $table->dateTime('prversao')->nullable()->default(null);
            $table->integer('prstatus')->default(1);            
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            
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
        Schema::dropIfExists('passrecovery');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 
    }
};
