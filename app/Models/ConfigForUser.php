<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class ConfigForUser extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'configforuser';
    protected $fillable = ['identificacao','exemplo','tipodado','status','classificacao','valor1','valor2','correspondenciarecebprotocolada','correspondenciaenviaprotocolada','diassessoesordinarias','horasessoesordinarias','temredacaofinal','transtatus'];
    protected $guarded = ['id', 'versao'];
    protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
        'flagcontrole' => 0,
    ];
    public $timestamps = false;
}
