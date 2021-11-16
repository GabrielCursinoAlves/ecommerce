<?php 
	
	namespace Hcode;

	use Rain\Tpl; 
	
	class Mailer{

	    const USERNAME = "ga72147@gmail.com";
	    const NAME_FROM = "Hcode Store";
	    const PASSWORD = "";

	    private $mail;

	    public function __construct($toAddress,$toName,
	    $subject,$tplName,$data = []){

	    	$config = array(
				"tpl_dir"=> $_SERVER['DOCUMENT_ROOT']."/views/email/",
				"cache_dir"=> $_SERVER['DOCUMENT_ROOT']."/views-cache/",
				"debug"=> false 
			);

			Tpl::configure($config);

			$tpl = new Tpl;

			foreach($data as $key => $value){

				$tpl->assign($key,$value);
			}

			$html = $tpl->draw($tplName,true);

	    	//Create a new PHPMailer instance
			$this->mail = new \PHPMailer();

			//Tell PHPMailer to use SMTP
			$this->mail->isSMTP();

			$this->mail->SMTPDebug = 0;

			//Set the hostname of the mail server
			$this->mail->Host = 'smtp.gmail.com';
			
			$this->mail->Port = 587;

			$this->mail->SMTPSecure = 'tls';

			//Whether to use SMTP authentication
			$this->mail->SMTPAuth = true;

			$this->mail->Username = self::USERNAME;

			//Password to use for SMTP authentication
			$this->mail->Password = self::PASSWORD;

			//Set who the message is to be sent from
			$this->mail->setFrom(self::USERNAME,self::
			NAME_FROM);

			//Set who the message is to be sent to
			$this->mail->addAddress($toAddress, $toName);

			//Set the subject line
			$this->mail->Subject = $subject;

			// UTF-8
			$this->mail->CharSet = 'UTF-8';

			//convert HTML into a basic plain-text alternative body
			$this->mail->msgHTML($html);

			//Replace the plain text body with one created manually
			//$this->mail->AltBody = '<h1>Título do E-mail</h1>
			//<p>Teste de envio de e-mail do curso php 7</p>
			//';
	    }

	    public function Send(){

	    	return $this->mail->send();
	    }
	}
	

?>