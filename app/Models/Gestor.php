<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class Gestor extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'gestor';
    protected $fillable = ['identificacao','nomefantasia','lema','datacadastro','cnpj','fkidcidade','endereco','fkidramoatividade','bairro', 'cep',
        'numero','complemento','inscrestadual','horariofuncionamentomanha','horariofuncionamentotarde','obs'];
    protected $guarded = ['id', 'versao'];
    public $timestamps = false;
    protected $attributes = [
        'flagexibe' => 0,
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
        'flagcontrole' => 0,
    ];

    public function cidade()
    {
        return $this->hasOne(Cidade::class, 'id', 'fkidcidade');
    }


}
