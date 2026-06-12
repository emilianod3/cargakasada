<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class EmailUnidade extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'emailunidade';
    protected $fillable = ['euemail','euanotacao','fkidunidade','flagexibe','flagdelete','flagatualiza','flaguser'];
    protected $guarded = ['id', 'euversao'];
    public $timestamps = false;

    public function unidade()
    {
        return $this->hasOne(Unidade::class, 'id', 'fkidunidade')->with('usuario');
    }       
}
