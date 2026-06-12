<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class Funcionario extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'funcionario';
    protected $fillable = ['fkidunico','fcdataentrada','fcdatasaida','fcstatus','fkidunidade','fkidcargo','fcmatricula','fcseriecarteira','fcnumcarteira','fccodigocarteria','fcvigenciacarteirainicio','fcvigenciacarteirafinal','fclinkcarteira','fcobs','fcdatacadastro','fkidgestor'];
    protected $guarded = ['id', 'fcversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagexibe' => 1,
        'flagdelete' => 0,
        'flagatualiza' => 1,
        'flaguser' => 0,
    ];

    public function unico()
    {
        return $this->hasOne(Unico::class, 'id', 'fkidunico')->with('avatar','cidade');
    }
    
    public function unidade()
    {
        return $this->hasOne(Unidade::class, 'id', 'fkidunidade');
    } 
    
    public function cargo()
    {
        return $this->hasOne(Cargo::class, 'id', 'fkidcargo');
    }
    
    public function arquivos(){
        return $this->hasMany(FuncionarioArq::class, 'fkidfuncionario', 'id');
    }
    
    public function emails(){
        return $this->hasMany(Email::class, 'fkidunico', 'fkidunico');
    }
    
    public function fones(){
        return $this->hasMany(Email::class, 'fkidunico', 'fkidunico');
    }    

}
