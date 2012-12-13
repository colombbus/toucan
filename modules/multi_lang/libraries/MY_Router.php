<?php
/**
 * Router for Multi_Lang Module
 *
 * @package    Multi_Lang Module 
 * @author     Kiall Mac Innes
 * @copyright  (c) 2007-2009 Multi_Lang Module Team..
 * @license    http://dev.kohanaphp.com/wiki/multilang/License
 */
class Router extends Router_Core {
	
	public static $language = '';
	
	public static function find_uri()
	{
		parent::find_uri();
		
		// TODO: Swap the !in_array() for a routes style regex... see Feature #2
		if (Kohana::config('multi_lang.enabled') and !in_array(self::$current_uri, Kohana::config('multi_lang.ignore_uri')))
		{
			$allowed_languages = Kohana::config('multi_lang.allowed_languages');
			
			if (preg_match('~^[a-z]{2}(?=/|$)~i', self::$current_uri, $matches) AND isset($matches[0]))
		    {
				// LC the language used in the url.
				$lang_lc = strtolower($matches[0]);
				
				// Check for invalid language in URL
				if ( ! array_key_exists($lang_lc, $allowed_languages))
					Event::run('system.404');
					
				// Set the currently defined language
				self::$language = $lang_lc;
				
				// Remove the language from the URI
				self::$current_uri = substr(self::$current_uri, 3);
				
				if (self::$current_uri == '')
				{
					// Make sure the default route is set
					$routes = Kohana::config('routes');
					
					if ( ! isset($routes['_default']))
						throw new Kohana_Exception('core.no_default_route');
		
					// Use the default route when no segments exist
					self::$current_uri = $routes['_default'];
				}
				
				Kohana::config_set('locale.language', array($allowed_languages[self::$language]));
				
				// GNU GetText Stuff
				if (function_exists('_'))
				{
					setlocale(LC_ALL, $allowed_languages[self::$language]);
					putenv('LC_ALL='.$allowed_languages[self::$language]);
					bindtextdomain("application", DOCROOT."/application/i18n");
					bindtextdomain("system", DOCROOT."/system/i18n");
					textdomain("application");
				}
				
				// Overwrite setlocale which has already been set before in Kohana::setup(), and a few lines up.
				setlocale(LC_ALL, $allowed_languages[self::$language].'.UTF-8');
				
				// Finally set a language cookie for 60 days
				cookie::set('language', self::$language, 5184000);
			}
			else
			{
				// Pick a language for the user and redirect
				
				// 1. Check for a language cookie. 
				$new_langs[] = (string) cookie::get('language');
				
				// 2. Look at HTTP_ACCEPT_LANGUAGE header
				foreach (Kohana::user_agent('languages') as $language)
				{
					$new_langs[] = substr($language, 0, 2);
				}
				
				// 3. Final hard-coded fallback from config file
				$new_langs[] = Kohana::config('multi_lang.fallback_language');
				
				// Now loop through the new languages and stop at the first valid one
				foreach ($new_langs as $new_lang)
				{
					if (array_key_exists($new_lang, $allowed_languages))
						break;
				}
				
				// Redirect the user so the language appears in the browser url
				self::$language = strtolower($new_lang); // Needed to allow url::site to give a correct url...
				
				$redirect_url = self::$current_uri;
				
				// We don't want to loose the query string
				$redirect_url .= ( ! empty($_SERVER['QUERY_STRING'])) ? '?'.trim($_SERVER['QUERY_STRING'], '&/') : '';
				
				url::redirect($redirect_url);
			}
		}
	}
}
