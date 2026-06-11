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
        if(ENV('DATABASE_DELETEONCREATE') == 'SIM') {
            self::down();
        }
        Schema::create('foneunidade', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('funumero', 50)->default('');
            //$table->foreignId('fkidtipofone')->nullable()->constrained('tipofone')->cascadeOnUpdate()->nullOnDelete()->default(0);
            $table->bigInteger('fkidtipofone')->default(0);
            $table->string('fuanotacao', 50)->default('');
            //$table->foreignId('fkidunidade')->nullable()->constrained('unidade')->cascadeOnUpdate()->nullOnDelete()->default(0);
            $table->unsignedBigInteger('fkidunidade');
            $table->foreign('fkidunidade')->references('id')->on('unidade')->constrained('unidade')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->dateTime('fuversao')->nullable()->default(null);
            $table->index(['fkidtipofone', 'fkidunidade']);
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
        Schema::dropIfExists('foneunidade');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
