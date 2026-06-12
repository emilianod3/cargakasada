<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class UnidadeContrato extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'unidadecontrato';
    protected $fillable = ['ucidentificacao','fkidunidade','fkidtipoarq','uctexto','ucarq','ucdatainiciovigencia','ucdatafinavigencia','ucdatacadastro','ucnumerocontrato','ucextensao','ucmyme','ucnomearquivo'];
    protected $guarded = ['id', 'ucversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagexibe' => 0,
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
    ];
    
    public function unidade()
    {
        return $this->hasOne(Unidade::class, 'id', 'fkidunidade');
    }     
}
