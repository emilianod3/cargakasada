<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class Email extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'email';
    protected $fillable = ['ememail','emanotacao','fkidunico','flagdelete','flagatualiza','flagexibe','flaguser','emusarcomoprincipal'];
    protected $guarded = ['id', 'emversao'];
    public $timestamps = false;

    public function unico()
    {
        return $this->hasOne(Unico::class, 'id', 'fkidunico')->with('usuario');
    }    
}
