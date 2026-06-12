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
        //Schema::dropIfExists('calpermissao');
        Schema::create('calpermissao', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('cppermissao', 250);
            //$table->foreignId('fkidcal')->nullable()->constrained('cal')->cascadeOnUpdate()->nullOnDelete()->default(null);
            //$table->unsignedBigInteger('fkidcal');
            //$table->foreignId('fkidcal')->constrained();
            //$table->unsignedBigInteger('user_id');
            //$table->foreign('fkidcal')->references('id')->on('cal');
            //$table->unsignedBigInteger('fkidcal');
            //$table->foreign('fkidcal')->references('id')->on('cal')->constrained('cal')->cascadeOnUpdate()->restrictOnDelete();
            //$table->foreignId('fkidgrupo')->nullable()->constrained('grupo')->cascadeOnUpdate()->nullOnDelete()->default(null);
        
            $table->unsignedBigInteger('fkidcal');
            $table->foreign('fkidcal')->references('id')->on('cal')->constrained('cal')->cascadeOnUpdate()->restrictOnDelete();
            $table->unsignedBigInteger('fkidgrupo');
            $table->foreign('fkidgrupo')->references('id')->on('grupo')->constrained('grupo')->cascadeOnUpdate()->restrictOnDelete();
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->integer('flagcontrole')->default(0);
            $table->dateTime('cpversao')->nullable()->default(null);
            //$table->index(['fkidgrupo', 'fkidcal']);

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
        Schema::dropIfExists('calpermissao');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

    }
};
