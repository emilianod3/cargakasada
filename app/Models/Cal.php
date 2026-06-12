<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class Cal extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'cal';
    protected $fillable = ['clidentificacao','clobserve','clrota','cltipo','clbase','clstatus'];
    protected $guarded = ['id', 'clversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
        'flagcontrole' => 0,
    ];


    
    public function menus()
    {
        return $this->hasMany(Menu::class, 'fkidcal', 'id');
    }


    public function permissao()
    {
        return $this->hasOne(CalPermissao::class, 'fkidcal', 'id');
    }


    public function permissoes()
    {
        return $this->hasMany(CalPermissao::class, 'fkidcal', 'id');
    }

}
