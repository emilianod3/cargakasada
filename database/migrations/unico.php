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
        Schema::create('unico', function (Blueprint $table) {
            Tools::setDatabaseEngine($table);
            $table->bigIncrements('id');
            $table->string('unidentificacao', 250)->default('');
            //$table->foreignId('fkidtipocadastro')->nullable()->constrained('tipocadastro')->cascadeOnUpdate()->nullOnDelete()->default(1);
            $table->unsignedBigInteger('fkidtipocadastro');
            $table->foreign('fkidtipocadastro')->references('id')->on('tipocadastro')->constrained('tipocadastro')->cascadeOnUpdate()->restrictOnDelete();
            $table->string('unapelido', 250)->default('0');
            $table->string('untiposanguineo', 30)->default('');
            $table->string('unnomefantasia', 250)->default('');
            $table->string('uncep', 250)->default('');
            $table->string('uninscrmunicipal', 250)->default('');
            $table->string('unserie', 250)->default('');
            $table->date('undatacadastro')->nullable()->default(null);  
            $table->date('undatanasc')->nullable()->default(null);
            $table->string('unrg', 50)->default('');
            $table->string('untituloeleitor', 50)->default('');
            $table->string('unnumcarttrabalho', 50)->default('');
            $table->string('uncpf', 50)->default('');
            $table->string('unpis', 50)->default('');
            $table->string('unzonaeleitoral', 250)->default('');
            $table->string('unsecaoeleitoral', 250)->default('');
            $table->string('uncnpj', 50)->default('');
            $table->bigInteger('fkidclassesocial')->default(0);          
            $table->bigInteger('fkidprofissao')->default(0);
            $table->bigInteger('fkidramoatividade')->default(0);
            $table->bigInteger('fkidescolaridade')->default(0);
            $table->bigInteger('fkidtratamento')->default(0);
            $table->bigInteger('fkidestadocivil')->default(0);
            $table->bigInteger('fkidcidade')->default(0);
            $table->bigInteger('fkidraca')->default(0);
            $table->text('unendereco');
            $table->string('unbairro', 250)->default('');
            $table->string('unnumero', 250)->default('');
            $table->text('uncomplemento');
            $table->char('unsexo', 1)->default('M');
            $table->string('undesignacao', 50)->default('');
            $table->string('uninscrestadual', 50)->default('');
            $table->text('unobs');
            $table->integer('unoptasimples')->default(0);
            $table->integer('unstatus')->default(1);
            $table->dateTime('unversao')->nullable()->default(null); 
            //$table->bigInteger('fkidgestor')->default(1);
            $table->unsignedBigInteger('fkidgestor');
            $table->foreign('fkidgestor')->references('id')->on('gestor')->constrained('gestor')->cascadeOnUpdate()->restrictOnDelete();
            $table->bigInteger('fkidunidade')->default(0); 
            $table->integer('flagexibe')->default(0);
            $table->integer('flagdelete')->default(0);
            $table->integer('flaguser')->default(0);
            $table->integer('flagatualiza')->default(0);
            $table->integer('flagcontrole')->default(0);
            $table->index(['fkidtipocadastro', 'fkidclassesocial', 'fkidprofissao']);  
            $table->index(['fkidramoatividade', 'fkidescolaridade', 'fkidtratamento']);
            $table->index(['fkidestadocivil', 'fkidcidade', 'fkidraca']);
            $table->index(['fkidunidade']);
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
        Schema::dropIfExists('unico');
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');          
    }
};
