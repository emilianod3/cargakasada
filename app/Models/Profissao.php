<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class Profissao extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'profissao';
    protected $fillable = ['pfidentificacao','flagdelete','flagatualiza','flaguser','fkidgestor'];
    protected $guarded = ['id', 'pfversao'];
    public $timestamps = false;
}
