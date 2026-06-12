<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;


class Cidade extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'cidade';
    protected $fillable = ['cdidentificacao','fkidestado'];
    protected $guarded = ['id', 'cdversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
        'flagcontrole' => 0,
    ];

    public function estado()
    {
        return $this->hasOne(Estado::class, 'id', 'fkidestado');
    }

}
