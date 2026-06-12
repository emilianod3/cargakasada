<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class Fornecedor extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'fornecedor';
    protected $fillable = ['fidentificacao','fcnpj','frazaosocial','femail','ftelefone','fcelular','fobs','fcep','fkidcidade','fendereco','fbairro','fnumero','fcomplemento','fobs','fdatacadastro','fkidgestor','fkidunidade'];
    protected $guarded = ['id', 'fversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagexibe' => 1,
        'flagdelete' => 0,
        'flagatualiza' => 1,
        'flaguser' => 0,
    ];
}

