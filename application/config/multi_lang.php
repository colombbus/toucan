<?php
/**
 * Config for Multi_Lang Module
 *
 * @package    Multi_Lang Module 
 * @author     Kiall Mac Innes
 * @copyright  (c) 2007-2009 Multi_Lang Module Team..
 * @license    http://dev.kohanaphp.com/wiki/multilang/License
 */
$config['enabled'] = true; // Enable or disable...

// The supported languages located in the i18n folder of your application
$config['allowed_languages'] = array
(
//	'en' => 'en_GB',
 	'fr' => 'fr_FR',
 	'es' => 'es_ES',
// 	'ca' => 'ca_ES',
);
$config['langs']=array(
	'es'=>'Español',
	'fr'=>'Français'
);
$config['fallback_language'] = 'fr';
//$config['fallback_language'] = $config['language']; // Fallback to the default language - set to FALSE to disable fallback.
													// WARNING: Kohana::lang() doesn't respect this option.

													// A list of URLs to not prefix a language to. eg media controllers, ajax controllers etc.
// I'll probably switch this out for a routes style regex at some point.											
$config['ignore_uri'] = array(
	'ajax/cities',
);

?>
