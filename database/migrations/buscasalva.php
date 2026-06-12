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
        Schema::create('buscasalva', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->text('bsdescricao')->nullable()->default(null);
            $table->text('bscampos')->nullable()->default(null);
            $table->text('subcalaux')->nullable()->default(null);
            $table->integer('subcalaux1')->nullable()->default(null);
            $table->integer('bsfixada')->default(0);
            $table->unsignedBigInteger('fkidgestor');
            $table->foreign('fkidgestor')->references('id')->on('gestor')->constrained('gestor')->cascadeOnUpdate()->restrictOnDelete();
            /*
            $table->foreignId('fkidusuario')->nullable()->constrained('usuario')->onUpdate('cascade')->onDelete('set null')->default(0);
            $table->foreignId('fkidcal')->nullable()->constrained('cal')->onUpdate('cascade')->onDelete('set null')->default(0);*/

            $table->unsignedBigInteger('fkidcal');
            $table->foreign('fkidcal')->references('id')->on('cal')->constrained('cal')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('fkidusuario');
            $table->foreign('fkidusuario')->references('id')->on('usuario')->constrained('usuario')->cascadeOnUpdate()->restrictOnDelete();
            /*            $table->unsignedBigInteger('fkidgestor');
            $table->foreign('fkidgestor')->references('id')->on('gestor')->constrained('gestor')->cascadeOnUpdate()->restrictOnDelete();*/

            $table->integer('bspublico')->default(0);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->dateTime('bsversao')->nullable()->default(null);
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
        Schema::dropIfExists('buscasalva');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1'); 
    }
};
