<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Auth;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class Usuario extends Model implements Auditable
{
    use HasFactory, Auditingtable;
    protected $table = 'usuario';
    protected $primaryKey = 'id';
    protected $fillable = ['ulogin','upassword','ustatus','ugestor','uhash','udatatermosuso','ucontadoracesso','uanotation', 'udatacadastro', 'udataultimoacesso', 'fkidunico', 'fkidgrupo', 'uaceitetermosuso', 'udataaceitetermosuso', 'usolicitalocalizacao','fkidgestor','flagdelete','flaguser','flagatualiza'];
    protected $guarded = ['uversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
        'flagcontrole' => 0,
    ];
    
    public function grupo()
    {
        return $this->hasOne(Grupo::class, 'id', 'fkidgrupo');
    }

    public function unico()
    {
        return $this->hasOne(Unico::class, 'id', 'fkidunico')->with('avatar');
    }    

    public function gestor()
    {
        return $this->hasOne(Gestor::class, 'id', 'fkidgestor');
    }

    public function emailprincipal()
    {
        return $this->hasOne(Email::class, 'fkidunico', 'fkidunico')->where('emusarcomoprincipal', 1);
    }

    public function emailparacontato()
    {
        return $this->hasOne(Email::class, 'fkidunico', 'fkidunico')->OrderBy('emusarcomoprincipal', 'desc');
    }

    public function foneprincipalcelular(){
        return $this->hasOne(Fone::class, 'fkidunico', 'fkidunico')->where('fkidtipofone', 3);
    }

    public function emails()
    {
        return $this->hasMany(Email::class, 'fkidunico', 'fkidunico');
    }

    public function fone()
    {
        return $this->hasOne(Fone::class, 'fkidunico', 'fkidunico');
    }

    
    public function getAuthIdentifier()
    {
        return Session::get('user')->id;
    }





}
