<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class RamoAtividade extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'ramoatividade';
    protected $fillable = ['rtidentificacao','rtcnaeversao','rtcnaesecao','rtcnaedivisao','rtcnaegrupo','rtcnaeclasse','rtcnaesubclasse','flagdelete','flagatualiza','flaguser'];
    protected $guarded = ['id', 'rtversao'];
    public $timestamps = false;
}
