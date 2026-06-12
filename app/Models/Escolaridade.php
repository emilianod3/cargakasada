<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class Escolaridade extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'escolaridade';
    protected $fillable = ['ecidentificacao'];
    protected $guarded = ['id', 'ecversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
        'flagcontrole' => 0,
    ];    
}
