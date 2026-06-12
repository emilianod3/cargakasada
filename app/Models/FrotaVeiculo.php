<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class FrotaVeiculo extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'frotaveiculo';
    protected $fillable = ['fvidentificacao','fvplaca','fvchassi','fvfabricacao','fvmodelo','fvobs','fvstatus','fkidgestor'];
    protected $guarded = ['id', 'fvversao'];
    public $timestamps = false;
	protected $attributes = [
        'flagdelete' => 0,
        'flagatualiza' => 1,
        'flaguser' => 0,
    ];

    public function kms()
    {
        return $this->hasMany(Frotakm::class, 'fkidfrotaveiculo', 'id')->where('fkstatus', 1);
    }

    public function abastecimentos()
    {
        return $this->hasMany(FrotaAbastecimento::class, 'fkidfrotaveiculo', 'id')->where('fastatus', 1);
    }    
}
