<?php

namespace Hcode;

use \Rain\Tpl;

use \Hcode\Model\Usuario;

class Mailer {

		const USERNAME="felipenomarques@gmail.com";
		const PASSWORD="nolasco22?";
		const NAME_FROM='Hcode Store';//nome do remetente , vai como mensaagem
		private $email;



	public function __construct($toAddress,$toName,$subject,$tplName,$data=array()){




	//array de configuração do tpl para email
	$config=array(
		"tpl_dir"=>$_SERVER['DOCUMENT_ROOT']."/views/email/",
		"cache_dir"=>$_SERVER['DOCUMENT_ROOT']."/cache-views/",
		 "debug"         => false

	);


	Tpl::configure($config);
	$tpl=new Tpl();


//pagando os dados do array e jogando  no assign do tpl
	foreach ($data as $key => $value) {
		# code...
		$tpl->assign($key,$value);
	}

//criando a var html e pegando o template
//e jogando dentro da var html , o parametro true e para q nao mostre o template
//e so jogue na var	
	$html=$tpl->draw($tplName,true);




			//Create a new PHPMailer instance
$this->mail = new \PHPMailer();

//Tell PHPMailer to use SMTP
$this->mail->isSMTP();

//Enable SMTP debugging
//SMTP::DEBUG_OFF = off (for production use)
//SMTP::DEBUG_CLIENT = client messages
//SMTP::DEBUG_SERVER = client and server messages
$this->mail->SMTPDebug = 0; //coloca zero para nao aparecer a msg, simulando como se estivesse em produção

//Set the hostname of the mail server
$this->mail->Host = 'smtp.gmail.com';
//Use `$this->mail->Host = gethostbyname('smtp.gmail.com');`
//if your network does not support SMTP over IPv6,
//though this may cause issues with TLS

//Set the SMTP port number:
// - 465 for SMTP with implicit TLS, a.k.a. RFC8314 SMTPS or
// - 587 for SMTP+STARTTLS
$this->mail->Port = 587;

//Set the encryption mechanism to use:
// - SMTPS (implicit TLS on port 465) or
// - STARTTLS (explicit TLS on port 587)
$this->mail->SMTPSecure = 'tls';

//Whether to use SMTP authentication
$this->mail->SMTPAuth = true;

//Username to use for SMTP authentication - use full email address for gmail
//$mail->Username = 'felipeno22php7@gmail.com';

//Password to use for SMTP authentication
//  $mail->Password = 'ctmondavgzjurlhy';

$this->mail->Username = Mailer::USERNAME;
$this->mail->Password = Mailer::PASSWORD;

//Set who the message is to be sent from
//Note that with gmail you can only use your account address (same as `Username`)
//or predefined aliases that you have configured within your account.
//Do not use user-submitted addresses in here
//$mail->setFrom('felipeno22php7@gmail.com', 'Curso PHP 7');
$this->mail->setFrom(Mailer::USERNAME, Mailer::NAME_FROM);

//Set an alternative reply-to address
//This is a good place to put user-submitted addresses
//$mail->addReplyTo('replyto@example.com', 'First Last');

//Set who the message is to be sent to
//$mail->addAddress('felipeno22php7@gmail.com', 'Suporte Hcode');
$this->mail->addAddress($toAddress, $toName);

//Set the subject line
$this->mail->Subject = $subject;

//Read an HTML message body from an external file, convert referenced images to embedded,
//convert HTML into a basic plain-text alternative body
//$mail->msgHTML(file_get_contents('contents.html'), __DIR__);
$this->mail->msgHTML($html);

//Replace the plain text body with one created manually
$this->mail->AltBody = 'This is a plain-text message body';

//Attach an image file
//$mail->addAttachment('images/phpmailer_mini.png');




		
//send the message, check for errors
//if (!$mail->send()) {
  //  echo 'Mailer Error: ' . $mail->ErrorInfo;
//} else {
  //  echo 'Message sent!';
    //Section 2: IMAP
    //Uncomment these to save your message in the 'Sent Mail' folder.
    #if (save_mail($mail)) {
    #    echo "Message saved!";
    #}
//}


	}


	public function send(){

			return $this->mail->send();

	}

}








?>