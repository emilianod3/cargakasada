<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class Unico extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'unico';
    protected $fillable = ['unidentificacao','fkidtipocadastro','unapelido','untiposanguineo','unnomefantasia','uncep','uninscrmunicipal','unserie','undatacadastro','undatanasc','unrg',
        'untituloeleitor','unnumcarttrabalho','uncpf','unpis','unzonaeleitoral','unsecaoeleitoral','uncnpj','fkidclassesocial','fkidprofissao','fkidramoatividade','fkidescolaridade',
        'fkidtratamento','fkidestadocivil','fkidcidade','fkidraca','unendereco','unbairro','unnumero','uncomplemento','unsexo','undesignacao','uninscrestadual','unobs', 'unoptasimples','unstatus','fkidgestor', 'fkidunidade'];
    protected $guarded = ['id', 'unversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
        'flagexibe' => 0,
        'flagcontrole' => 0,
    ];

    public function getDataNascBrAttribute()
    {        
        return $this->undatanasc != '' ? date('d/m/Y', strtotime($this->undatanasc)) : '';
    }

    public function getFullIdentifAttribute()
    {        
        return ($this->unidentificacao != '' ? $this->unidentificacao : '') . ($this->unapelido != '' ? ' ('.$this->unapelido.')' : '');
    }

    public function getArtigoSexoAttribute()
    {        
        return ($this->unsexo != 'F' ? 'o' : 'a');
    }    

    public function getIdentificacaoAttribute()
    {        
        if($this->unidentificacao != ''){
            return $this->unidentificacao;
        }else if($this->unnomefantasia != ''){
            return $this->unnomefantasia;
        }else{
            return  '';
        }        
    }

    public function getApelidoAttribute()
    {        
        if($this->unapelido != ''){
            return $this->unapelido;
        }else{
            return  '';
        }        
    }  

    public function getApelidoParentesesAttribute()
    {        
        if($this->unapelido != ''){
            return '('.$this->unapelido.')';
        }else{
            return  '';
        }        
    }

    public function avatar()
    {
        //return $this->hasOne(UnicoArq::class, 'fkidunico', 'id')->where('uastatus', 1)->where('flagexibe', 1)->where('fkidtipoarq', 8);
        //return $this->hasMany(UnicoArq::class, 'fkidunico', 'id');        
        return $this->hasOne(UnicoArq::class, 'fkidunico', 'id')->where('fkidtipoarq', 8);
    
    }

    public function fotoCapa()
    {
        return $this->hasOne(UnicoArq::class, 'fkidunico', 'id')->where('uastatus', 1)->where('flagexibe', 1)->where('fkidtipoarq', 34);
    }

    public function fotosGaleriaPessoal()
    {
        return $this->hasMany(UnicoArq::class, 'fkidunico', 'id')->where('uastatus', 1)->where('flagexibe', 1)->where('fkidtipoarq', 35);
    }
    
    public function usuario(){
        return $this->hasOne(Usuario::class, 'fkidunico', 'id')->where('ustatus', 1);
    }    

    public function cidade(){
        return $this->hasOne(Cidade::class, 'id', 'fkidcidade')->with('estado');
    }

    public function email(){
        return $this->hasMany(Email::class, 'fkidunico', 'id');
    }

    public function emailprincipal(){
        return $this->hasOne(Email::class, 'fkidunico', 'id')->where('emusarcomoprincipal', 1);
    }

    public function gestor()
    {
        return $this->hasOne(Cliente::class, 'id', 'fkidgestor');
    }

    public function fone(){
        return $this->hasMany(Fone::class, 'fkidunico', 'id');
    }
        
    public function foneprincipalcelular(){
        return $this->hasOne(Fone::class, 'fkidunico', 'id')->where('fkidtipofone', 3);
    }

    public function foneprincipal(){
        return $this->hasOne(Fone::class, 'fkidunico', 'id')->where('fkidtipofone', 2);
    }

    public function anexos()
    {
        return $this->hasmany(UnicoArq::class, 'fkidunico', 'id')->with('tipo')->orderBy('id','asc');
    }
}
