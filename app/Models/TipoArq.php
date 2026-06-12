<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class TipoArq extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'tipoarq';
    protected $fillable = ['tpaidentificacao','tpaplural','tpaclassificacao','tpastatus','fkidgestor'];
    protected $guarded = ['id', 'tpaversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagexibe' => 1,
        'flagdelete' => 0,
        'flagatualiza' => 1,
        'flaguser' => 0,
        'flagcontrole' => 0,
    ];



    public function cal()
    {
        return $this->hasOne(Cal::class, 'id', 'tpaclassificacao');
    }  
}
