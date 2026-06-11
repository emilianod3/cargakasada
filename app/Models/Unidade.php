<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use \OwenIt\Auditing\Auditable as Auditingtable;
use OwenIt\Auditing\Contracts\Auditable;

class Unidade extends Model implements Auditable
{
    use HasFactory, Auditingtable;

    protected $table = 'unidade';
    protected $fillable = ['undidentificacao','undclienteprincipal','undtexto','unnomefantasia','undcep','undinscrmunicipal','unddatacadastro','unddataasscontrato','unddatainicontrato',
        'unddatafimcontrato','uncnpj','fkidcidade','fkidramoatividade','undendereco','undbairro','undnumero','undcomplemento','unddesignacao','undinscrestadual','unhorariofuncionamentomanha',
        'uncomochegar','ungeorreferencia','unhistoria', 'fkidgestor', 'unhorariofuncionamentotarde','undobs','unlema','undversao','unsiteativo','untiposite','unmodelosite'];
    protected $guarded = ['id', 'undversao'];
    public $timestamps = false;
    protected $attributes = [
        'flagexibe' => 0,
        'flagdelete' => 0,
        'flaguser' => 0,
        'flagatualiza' => 0,
        'flagcontrole' => 0,
    ];
	
	
	public function cidade()
    {
        return $this->hasOne(Cidade::class, 'id','fkidcidade')->with('estado');
    }

    public function getCepAttribute()
    {
        if(strlen($this->undcep) > 0)
            return substr($this->undcep, 0,5).'-'.substr($this->undcep, 5,3);
        else
            return $this->undcep;
    }

    public function getTipoSiteAttribute()
    {
        switch($this->untiposite) {
            case 1:
                return 'Executivo';
                break;
            case 2:
                return 'Legislativo';
                break;
            case 3:
                return 'Autoarquia';
                break;                
            default:
                return 'Não Informado';
                break;
        }
    }


    public function fones()
    {
        return $this->hasMany(FoneUnidade::class, 'fkidunidade','id')->with('tipofone')->where('flagexibe',1)->orderBy('fuversao', 'DESC');
    }

    public function emails()
    {
        return $this->hasMany(EmailUnidade::class, 'fkidunidade','id')->where('flagexibe',1)->orderBy('euemail', 'ASC');
    }    
    
    public function sites()
    {
        return $this->hasMany(SiteUnidade::class, 'fkidunidade','id')->where('flagexibe',1)->orderBy('suendereco', 'ASC');
    }    
    
    public function brasao()
    {
        return $this->hasOne(UnidadeArq::class, 'fkidunidade', 'id')->where('uadstatus', 1)->where('flagexibe', 1)->where('fkidtipoarq', 40);
    }
    
    public function bandeira()
    {
        return $this->hasOne(UnidadeArq::class, 'fkidunidade', 'id')->where('uadstatus', 1)->where('flagexibe', 1)->where('fkidtipoarq', 41);
    } 
    
    public function anexos()
    {
        return $this->hasMany(UnidadeArq::class, 'fkidunidade', 'id')->where('uadstatus', 1)->where('flagexibe', 1);
    }
    
    public function estruturaorganizacional()
    {
        return $this->hasMany(UnidadeArq::class, 'fkidunidade', 'id')->where('uadstatus', 1)->where('flagexibe', 1)->where('fkidtipoarq', 43);
    }
	
	
}
