<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class Cargo extends Model implements Auditable
{
    use HasFactory, Auditingtable;
    protected $table = 'cargo';
    protected $fillable = ['clidentificacao'];
    protected $guarded = ['id', 'clversao', 'fkidgestor', 'clstatus'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
        'flagexibe' => 0,
    ];

    public function gestor()
    {
        return $this->hasOne(Gestor::class, 'id', 'fkidgestor');
    }
}
