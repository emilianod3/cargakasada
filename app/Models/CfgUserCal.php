<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;


class CfgUserCal extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'cfgusercal';
    protected $fillable = ['fkidusuario','fkidcal','uctipo','ucregporpagina','ucusefavorito','ucsavefavorito','ucusesizecolumnsrelative','ucmaximizado','ucsizefixo','ucsizerelative','ucwidth','ucheight','ucuseteclaatalho','ucstatus','ucoptsearch'];
    protected $guarded = ['id', 'ucversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
    ];
}

/*
uctipo = 1=numero de regs por pagina 2=outras configuracoes

*/
