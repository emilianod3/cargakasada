<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class ConfigUser extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'configuser';
    protected $fillable = ['fkidcal','fkidusuario','fkidconfigforuser','status'];
    protected $guarded = ['id', 'versao'];
    public $timestamps = false;
    protected $attributes = [
        'flagexibe' => 0,
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
    ];

    public function usuario()
    {
        return $this->hasOne(Usuario::class, 'id', 'fkidusuario')->with('unico');
    }

    public function configforuser()
    {
        return $this->hasOne(ConfigForUser::class, 'id', 'fkidconfigforuser');
    }



}
