<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class Endereco extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'endereco';
    protected $fillable = ['fkidcidade','endcep','endtipologradouro','endlogradouro','endbairro','endstatus'];
    protected $guarded = ['id', 'endversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
        //'flagexibe' => 0,
        'flagcontrole' => 0,
    ];

    public function cidade()
    {
        return $this->hasOne(Cidade::class, 'id', 'fkidcidade')->with('estado');
    }   
}
