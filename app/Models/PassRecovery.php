<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class PassRecovery extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'passrecovery';
    protected $fillable = ['fkidusuario','fkidemail','prip','token','prdtrecovery','prdtregistro','prversao','prstatus'];
    protected $guarded = ['id', 'prversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
    ];
}
