<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * SwiftMailer driver, used with the email helper.
 *
 * @see http://www.swiftmailer.org/wikidocs/v3/connections/nativemail
 * @see http://www.swiftmailer.org/wikidocs/v3/connections/sendmail
 * @see http://www.swiftmailer.org/wikidocs/v3/connections/smtp
 *
 * Put your own parameters here:
 * driver: valid drivers are: native, sendmail, smtp
 * sender: the email address to be used to send emails
 * hostanme: your hostname
 * username: username to access email server
 * password: password to access email server
 * to use secure connections with SMTP, set "port" to 465 instead of 25.
 * to enable TLS, set "encryption" to "tls".
*/


$config['driver'] = 'smtp';
$config['sender']='admin@yoursite.com';
$config['options'] = array('hostname'=>'your.hostname.com', 'port'=>'25', 'username'=>'admin@yoursite.com', 'password'=>'password');

