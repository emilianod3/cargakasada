<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class Config extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'config';
    protected $fillable = ['identificacao','exemplo','tipodado','status','classificacao','valor1','valor2'];
    protected $guarded = ['id', 'versao'];
    public $timestamps = false;
    protected $attributes = [
        'flagexibe' => 0,
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
        'flagcontrole' => 1,
    ];
    /*
    public function cfgsist()
    {
        return $this->hasOne(CfgSist::class, 'id', 'fkidconfig');
    }*/


}
