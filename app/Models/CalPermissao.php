<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class CalPermissao extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'calpermissao';
    protected $fillable = ['cppermissao','fkidcal','fkidgrupo'];
    protected $guarded = ['id', 'cpversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
        'flagcontrole' => 0,
    ];

    public function cal()
    {
        return $this->hasOne(Cal::class, 'id', 'fkidcal');
    }

    public function grupo()
    {
        return $this->hasOne(Grupo::class, 'id', 'fkidgrupo');
    }  
}
