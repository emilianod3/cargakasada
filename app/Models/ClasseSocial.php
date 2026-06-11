<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class ClasseSocial extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'classesocial';
    protected $fillable = ['clscidentificacao','flagdelete','flagatualiza','flaguser'];
    protected $guarded = ['id', 'clscversao'];
    public $timestamps = false;
}
