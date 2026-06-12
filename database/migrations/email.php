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
        Schema::create('email', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('euemail', 250)->default('');
            $table->string('euanotacao', 50)->nullable()->default('');
            //$table->foreignId('fkidunico')->nullable()->constrained('unico')->cascadeOnUpdate()->nullOnDelete()->default(0);
            $table->unsignedBigInteger('fkidunico');
            $table->foreign('fkidunico')->references('id')->on('unico')->constrained('unico')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('emusarcomoprincipal')->default(0);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->dateTime('emversao')->nullable()->default(null);
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
        Schema::dropIfExists('email');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
