<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class Frotakm extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'frotakm';
    protected $fillable = ['fkidfrotaveiculo','fkinicio','fkdatainicial','fkfinal','fkdatafinal','fkstatus','fkdestino','fkobs','fkidgestor'];
    protected $guarded = ['id', 'fkversao'];
    public $timestamps = false;
	protected $attributes = [
        'flagdelete' => 0,
        'flagatualiza' => 1,
        'flaguser' => 0,
    ];

    public function veiculo()
    {
        return $this->hasOne(FrotaVeiculo::class, 'id', 'fkidveiculo');
    }

    public function getKmInicioAttribute()
    {        
        return number_format($this->fkinicio, 3, '.', '');
    }

    public function getKmFinalAttribute()
    {        
        return number_format($this->fkfinal, 3, '.', '');
    }  
}
