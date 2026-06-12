<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class FrotaAbastecimento extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'frotaabastecimento';
    protected $fillable = ['fadata','fkidfrotaveiculo','falitros','favalorporlitro','favalortotal','fakm','fastatus','faobs','famediaultimoabastecimento','facombustivel','fkidgestor'];
    protected $guarded = ['id', 'faversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flagatualiza' => 1,
        'flaguser' => 0,
    ];

    public function veiculo()
    {
        return $this->hasOne(FrotaVeiculo::class, 'id', 'fkidfrotaveiculo');
    }

}
