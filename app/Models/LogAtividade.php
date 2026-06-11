<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class LogAtividade extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'logatividade';
    protected $fillable = ['idregistro','fkidusuario','fktipoacao','lgonde','lgtexto','llocal', 'lip', 'lagent'];
    protected $guarded = ['id', 'lgversao'];
    public $timestamps = false;
	protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
    ];
}
