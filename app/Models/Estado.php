<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class Estado extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'estado';
    protected $fillable = ['etidentificacao','etsigla','flagdelete','flagatualiza','flaguser'];
    protected $guarded = ['id', 'etversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
        'flagcontrole' => 0,
    ];
}
