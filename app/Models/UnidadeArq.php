<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class UnidadeArq extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'unidadearq';
    protected $fillable = ['udaidentificacao','fkidunidade','fkidtipoarq','uadextensao','uadmyme','uadtexto','uadtamanho','uadarq','uadstatus'];
    protected $guarded = ['id', 'uadversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagexibe' => 0,
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
    ];

    public function tipo()
    {
        return $this->hasOne(TipoArq::class, 'id', 'fkidtipoarq');
    }

    public function unidade()
    {
        return $this->hasOne(Unidade::class, 'id', 'fkidunidade');
    }     
}
