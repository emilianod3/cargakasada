<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class UnicoArq extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'unicoarq';
    protected $fillable = ['uaidentificacao','fkidunico','fkidtipoarq','uaextensao','uamyme','uatexto','uatamanho','uaarq','uapath','uasavetype','uastatus'];
    protected $guarded = ['id', 'uaversao'];
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

    public function unico()
    {
        return $this->hasOne(Unico::class, 'id', 'fkidunico');
    }    
}
