<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class TipoParentesco extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'tipoparentesco';
    protected $fillable = ['tppidentificacao'];
    protected $guarded = ['id', 'tppvversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flagatualiza' => 1,
        'flaguser' => 0,
        'flagcontrole' => 0,
    ];    
}
