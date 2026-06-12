<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class FuncionarioArq extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'funcionarioarq';
    protected $fillable = ['faidentificacao','fkidfuncionario','fkidtipoarq','fames','faano','faextensao','fatitulo','fatexto','fatamanho','famyme','faarq','fapath','fasavetype','fastatus','flagexibe','flagdelete','flagatualiza','flaguser'];
    protected $guarded = ['id', 'faversao'];
    public $timestamps = false;

    public function tipo()
    {
        return $this->hasOne(TipoArq::class, 'id', 'fkidtipoarq');
    }

    public function funcionario()
    {
        return $this->hasOne(Funcionario::class, 'id', 'fkidfuncionario');
    }    
    
}
