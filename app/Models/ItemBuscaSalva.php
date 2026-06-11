<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Findespesa;
use App\Http\Controllers\Core\Tools;
use Exception;
use Illuminate\Support\Carbon;

class ItemBuscaSalva extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'itembuscasalva';
    protected $fillable = ['fkidusuario','bccampo','bcvalor','fkidcolumncal','fkidbuscasalva','bcstatus','idoperador','fkidgestor'];
    protected $guarded = ['id', 'bcversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
    ];

    public function busca()
    {
        return $this->hasOne(Buscasalva::class, 'fkidbuscasalva', 'id');
    }


}
