<?php 
	
	namespace Hcode\Model;

	use \Hcode\DB\Sql;
	use \Hcode\Model;
	
	class Address extends Model{
	    
	    const SESSION_ERROR = "AddressError";
	    
	    public static function getCEP($nrcep){

	    	$nrcep = str_replace("-","",$nrcep);

	    	$ch = curl_init();

	    	// Opções que ele vai iniciar essa chamada
	    	curl_setopt($ch,CURLOPT_URL,"https://viacep.com.br/ws/$nrcep/json/");
	    	curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
	    	curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);

	    	$data = json_decode(curl_exec($ch),true);

	    	curl_close($ch);

	    	return $data;

	    }

	    public function loadFromCEP($nrcep){

	    	$data = static::getCEP($nrcep);
	    	
	    	if(isset($data['logradouro']) && $data['logradouro']){

	    		$this->setdesaddress($data['logradouro']);
	    		$this->setdescomplement($data['complemento']);
	    		$this->setdesdistrict($data['bairro']);
	    		$this->setdescity($data['localidade']);
	    		$this->setdesstate($data['uf']);
	    		$this->setdescountry('Brasil');
	    		$this->setdeszipcode($nrcep);
	    	}

	    }

	    public function save(){

	    	$sql = new Sql();

	    	$results = $sql->select("CALL sp_addresses_save(
	    	:idaddres,:idperson,:desaddress,:descomplement,:descity,
	    	:desstate,:descountry,:deszipcode,:desdistrict)",[
	    		":idaddres"=>$this->getidaddres(),
	    		":idperson"=>$this->getidperson(),
	    		":desaddress"=>$this->getdesaddress(),
	    		":descomplement"=>$this->getdescomplement(),
	    		":descity"=>$this->getdescity(),
	    		":desstate"=>$this->getdesstate(),
	    		":descountry"=>$this->getdescountry(),
	    		":deszipcode"=>$this->getdeszipcode(),
	    		":desdistrict"=>$this->getdesdistrict()
	    	]);

	    	if(count($results) > 0){
	    		$this->setData($results[0]);
	    	}
	    }

	    public static function setMsgErro($msg){

	  		$_SESSION[self::SESSION_ERROR] = $msg;
	  	}

	  	public static function getMsgError(){

	  		$msg = (isset($_SESSION[self::SESSION_ERROR])) ? 
	  		$_SESSION[self::SESSION_ERROR] : "";

	  		self::clearMsgError();

	  		return $msg;
	  	}

	  	public static function clearMsgError(){

	  		$_SESSION[self::SESSION_ERROR] = NULL;
	  	}
	}
	

?>