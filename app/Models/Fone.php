<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class Fone extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'fone';
    protected $fillable = ['fnnumero','fkidtipofone','fnanotacao','fkidunico'];
    protected $guarded = ['id', 'fnversao'];
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
    
    public function unico()
    {        
        return $this->hasOne(Unico::class, 'id', 'fkidunico');
    }


}
