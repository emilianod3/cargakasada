<?php

use App\Http\Controllers\Core\Tools;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;


return new class () extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Schema::dropIfExists('cal');
        Schema::create('cal', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('clidentificacao', 250)->default('');
            $table->text('clobserve')->nullable();
            $table->string('clbase', 250)->nullable()->default('');
            $table->string('clrota', 250)->nullable()->default('');
            $table->integer('cltipo')->default(1);
            $table->integer('clstatus')->default(1);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->integer('flagcontrole')->default(0);
            $table->dateTime('clversao')->nullable()->default(null);
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
        Schema::dropIfExists('cal');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
};
