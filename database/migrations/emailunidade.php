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
        Schema::create('emailunidade', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('euemail', 250)->default('');
            $table->string('euanotacao', 50)->default('');
            //$table->foreignId('fkidunidade')->nullable()->constrained('unidade')->cascadeOnUpdate()->nullOnDelete()->default(0);
            $table->unsignedBigInteger('fkidunidade');
            $table->foreign('fkidunidade')->references('id')->on('unidade')->constrained('unidade')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->dateTime('euversao')->nullable()->default(null);
            $table->index(['fkidunidade']);
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
        Schema::dropIfExists('emailunidade');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
