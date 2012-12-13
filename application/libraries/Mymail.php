<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
* Simple_Auth - user authorization library for KohanaPHP framework
*
* @author           thejw23
* @copyright        (c) 2009 thejw23
* @license          http://www.opensource.org/licenses/isc-license.txt
* @version          1.3
* @last change      set_role(), active_to      
* based on KohanaPHP Auth and Auto_Modeler
*/
class Mymail_Core  {

	

	public function __construct($config = array())
	{
	}

	public static function factory()
	{
		return new mymail();
	}

	public static function instance()
	{
		static $instance;

		// Load the Auth instance
		empty($instance) and $instance = new mymail();

		return $instance;
	}
	
	public function sendRegisterMail($dest,$username,$password,$lang){
		$config=Kohana::config('email');		
		$from= $config['sender'];
		$xml=new Xml_Model;
		$mail= $xml->obtientPartie('./lang/mail/register' . $lang. '.xml',$lang,'CODE');
		$subject = $mail['SUBJECT'];
		$message = $mail['PART1'] . $username . $mail['PART2'] . $password . $mail['PART3']; 
		email::send($dest, $from, $subject, $message, TRUE);		
	}
}
?>