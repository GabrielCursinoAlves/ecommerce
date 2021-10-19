<?php 
	
	namespace Hcode\Model;

	use \Hcode\DB\Sql;
	use \Hcode\Model;
	use \Hcode\Mailer;
	
	class User extends Model{

		const SESSION = "User";
		const SECRET = "HcodePhp7_Secret";
		const SECRET_IV = "HcodePhp7_Secret_IV";
		
		public static function getFromSession(){

			$user = new User();

			if(isset($_SESSION[self::SESSION]) &&
			  (int)$_SESSION[self::SESSION]['iduser'] > 0){

				$user->setData($_SESSION[self::SESSION]);
			}

			return $user;
		}

		public static function checkLogin($inadmin = true){

			if(!isset($_SESSION[self::SESSION]) ||
	    	   !$_SESSION[self::SESSION] ||
	    	   !(int)$_SESSION[self::SESSION]["iduser"] > 0){
			   
			   // Não está logado
			   return false;

			}else{

				if($inadmin === true && 
				  (bool)$_SESSION[self::SESSION] === true){

				  // Está logado é admin
				  return true;

				}else if($inadmin === false){

					// Está logado não é admin
					return true;

				}else{

					return false;
				}
			}
		}	

	    public static function login($login,$password){

	    	$sql = new Sql();

	    	$results = $sql->select("SELECT *FROM tb_users 
	    	WHERE deslogin = :login",[":login"=>$login]);

	    	if(count($results) === 0){
	    		throw new \Exception("Usuário inexistente ou senha inválida");
	    	}

	    	$data = $results[0];

	    	if(password_verify($password,$data['despassword'])){

	    		$user = new User();

	    		$user->setData($data);

	    		$_SESSION[USER::SESSION] = $user->getValues();

	    		return $user;

	    	}else{
	    		throw new \Exception("Usuário inexistente ou senha inválida");
	    	}
	    }

	    public static function verifylogin($inadmin = true){

	    	if(self::checkLogin($inadmin)){

	    		header("Location:/admin/login");
	    		exit;
	    	}
	    }

	    public static function logout(){

	    	$_SESSION[User::SESSION] = NULL;
	    }

	    public static function listall(){

	    	$sql = new Sql();

	    	return $sql->select("SELECT *FROM tb_users a 
	    	INNER JOIN tb_persons b USING(idperson) ORDER BY 
	    	b.desperson;");
	    }

	    public function save(){

	    	$sql = new Sql();

			$results = $sql->select("CALL 
			sp_users_save(:desperson,:deslogin,
			:despassword,:desemail,:nrphone,:inadmin)",[
				":desperson"=>$this->getdesperson(),
				":deslogin"=>$this->getdeslogin(),
				":despassword"=>$this->getdespassword(),
				":desemail"=>$this->getdesemail(),
				":nrphone"=>$this->getnrphone(),
				":inadmin"=>$this->getinadmin()  
			]);

			$this->setData($results[0]);

	    }

	    public function get($iduser){

	    	$sql = new Sql();

	    	$results = $sql->select("SELECT *FROM tb_users a 
	    	INNER JOIN tb_persons b USING(idperson) 
	    	WHERE a.iduser = :iduser",[
	    		":iduser"=>$iduser
	    	]);

	    	$this->setData($results[0]);
	    }

	    public function update(){

	    	$sql = new Sql();

			$results = $sql->select("CALL 
			sp_usersupdate_save(:iduser,:desperson,:deslogin,
			:despassword,:desemail,:nrphone,:inadmin)",[
				":iduser"=>$this->getiduser(),
				":desperson"=>$this->getdesperson(),
				":deslogin"=>$this->getdeslogin(),
				":despassword"=>$this->getdespassword(),
				":desemail"=>$this->getdesemail(),
				":nrphone"=>$this->getnrphone(),
				":inadmin"=>$this->getinadmin()  
			]);

			$this->setData($results[0]);

	    }

	    public function delete(){

	    	$sql = new Sql();

	    	$sql->query("CALL sp_users_delete(:iduser)",[
	    		":iduser"=>$this->getiduser()
	    	]);
	    }

	    public static function getforgot($email){

	    	$sql = new Sql();

	    	$results = $sql->select("SELECT *FROM 
	    	tb_persons a INNER JOIN tb_users b 
	    	USING(idperson) WHERE a.desemail = :email;",[
	    		":email"=>$email
	    	]);

	    	if(count($results) === 0){
	    		
	    		throw new \Exception("Não foi possível
	    		recuperar a senha");
	    	}else{

	    		$data = $results[0];

	    		$results_recoveries = $sql->select("CALL 
	    		sp_userspasswordsrecoveries_create(:iduser,:desip);
	    		",[
	    			":iduser"=>$data["iduser"],
	    			":desip"=>$_SERVER['REMOTE_ADDR']
	    		]);

	    		if(count($results_recoveries) === 0){

	    			throw new \Exception("Não foi possível
	    			recuperar senha");
	    		}else{

	    			$data_recoveries = $results_recoveries[0];

	    			$code = openssl_encrypt($data_recoveries[
	    			'idrecovery'], 'AES-128-CBC', 
	    			pack("a16", User::SECRET), 0, 
	    			pack("a16", User::SECRET_IV));

	    			$code = base64_encode($code);

	    			$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=".$code;

	    			$mailer = new Mailer($data["desemail"],
	    			$data["desperson"],"Redefinir Senha da
	    			Hcode Store","forgot",[
	    				"name"=>$data["desperson"],
	    				"link"=>$link
	    			]);

	    			$mailer->send();

	    		}
	    	}
	    }

	    public static function validForgotDecrypt($code){

			$code = base64_decode($code);

			$idrecovery = openssl_decrypt($code,'AES-128-CBC', 
		    pack("a16", User::SECRET), 0,pack("a16",
		    User::SECRET_IV));

		    $sql = new Sql();

		    $result = $sql->select("SELECT * FROM
		    tb_userspasswordsrecoveries a
			INNER JOIN tb_users b using(iduser)
			INNER JOIN tb_persons c using(idperson)
			WHERE idrecovery = :idrecovery AND a.dtrecovery IS 
			NULL AND DATE_ADD(a.dtregister,interval 1 HOUR) >
			NOW()
			;",[
				":idrecovery"=>$idrecovery
			]);

			if(count($result) === 0){
				
				throw new \Exception("Não foi possível
				recuperar senha");
			}else{

				return $result[0];
			}
		}

		public static function setForgotUsed($idrecovery){

			$sql = new Sql();

			$sql->query("UPDATE tb_userspasswordsrecoveries
			SET dtrecovery = NOW() 
			WHERE idrecovery = :idrecovery",[
				":idrecovery"=>$idrecovery
			]);
		}

		public function setPassword($password){

			$sql = new Sql();

			$sql->query("UPDATE tb_users SET despassword =
			:password WHERE iduser = :iduser",[
				":password"=>$password,
				":iduser"=>$this->getiduser()
			]);
		}

	}
	

?>