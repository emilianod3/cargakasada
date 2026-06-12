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
        Schema::create('unicoarq', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->text('uaidentificacao');
            //$table->bigInteger('fkidunico')->default(0);
            $table->unsignedBigInteger('fkidunico');
            $table->foreign('fkidunico')->references('id')->on('unico')->constrained('unico')->cascadeOnUpdate()->restrictOnDelete();
            $table->bigInteger('fkidtipoarq')->default(0);
            $table->string('uaextensao', 50)->default('');
            $table->text('uamyme');
            $table->longText('uatexto');
            $table->bigInteger('uatamanho')->default(0);
            $table->binary('uaarq')->nullable()->default(null);
            $table->longText('uapath')->nullable()->default(null);
            $table->integer('uasavetype')->default(1);  //1=salva na pasta 2=salva no banco 3=salva no banco e na pasta
            $table->integer('uastatus')->default(1);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->integer('flagexibe')->default(0);
            $table->dateTime('uaversao')->nullable()->default(null);
            $table->index(['fkidtipoarq', 'fkidunico']);
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
        Schema::dropIfExists('unicoarq');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');         
    }
};
