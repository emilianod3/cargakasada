<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class Menu extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'menu';
    protected $fillable = ['mnidentificacao','mnicone', 'mndestaque', 'mnskin', 'mnnumeracao', 'fkidcal', 'fkidmenunivelacima','mnsequencia', 'mnstatus',];
    protected $guarded = ['id', 'mnversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
        'flagcontrole' => 0,
    ];


    public function cal()
    {
        return $this->hasOne(Cal::class, 'id', 'fkidcal');
    }

    public function menuacima()
    {
        return $this->hasOne(Menu::class, 'id', 'fkidmenunivelacima');
    }
}
