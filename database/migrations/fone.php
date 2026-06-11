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
        Schema::create('fone', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('fnnumero', 50)->default('');
            $table->unsignedBigInteger('fkidtipofone');
            $table->foreign('fkidtipofone')->references('id')->on('tipo_fone')->constrained('tipo_fone')->cascadeOnUpdate()->restrictOnDelete();
            //$table->foreignId('fkidtipofone')->nullable()->constrained('tipofone')->cascadeOnUpdate()->nullOnDelete()->default(0);
            $table->string('fnanotacao', 50)->nullable()->default('');
            $table->foreignId('fkidunico')->nullable()->constrained('unico')->cascadeOnUpdate()->nullOnDelete()->default(0);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->dateTime('fnversao')->nullable()->default(null);
            $table->index(['fkidtipofone']);
            $table->index(['fkidunico']);
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
        Schema::dropIfExists('fone');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
