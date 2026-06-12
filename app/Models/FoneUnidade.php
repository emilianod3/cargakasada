<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class FoneUnidade extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'foneunidade';
    protected $fillable = ['funumero','fkidtipofone','fuanotacao','fkidunidade'];
    protected $guarded = ['id', 'fuversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
    ];


    public function tipofone()
    {
        return $this->hasOne(TipoFone::class, 'id', 'fkidtipofone');
    }
    
    public function unidade()
    {        
        return $this->hasOne(Unidade::class, 'id', 'fkidunidade');
    }


}
