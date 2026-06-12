<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;


class CfgSist extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'cfgsist';
    protected $fillable = ['fkidgestor','valor1','valor2','transtatus','fkidgestor','fkidconfig'];
    protected $guarded = ['id', 'tranversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
		'flagcontrole' => 1,
    ];

    public function config()
    {
        return $this->hasOne(Config::class, 'id', 'fkidconfig');
    }    
	
}
