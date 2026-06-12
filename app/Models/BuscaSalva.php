<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class BuscaSalva extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'buscasalva';
    protected $fillable = ['fkidusuario','bsdescricao','fkidcal','bspublico','flagdelete','flagatualiza','flaguser'];
    protected $guarded = ['id', 'bsversao'];
    public $timestamps = false;
}
