<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;


class ColumnCal extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'columncal';
    protected $fillable = ['fkidcal','clsize','clorder','clvisible','clheader','clstatus','clname','clalinhamento','cltipocampo'];
    protected $guarded = ['id', 'clversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
    ];

    public function cal()
    {
        return $this->hasOne(Cal::class, 'id', 'fkidcal');
    }
}
