<?php defined('SYSPATH') or die('No direct script access.');
/**
* URL Helper for Multi_Lang module
*
* @package    Multi_Lang Module 
* @author     Kiall Mac Innes
* @copyright  (c) 2007-2009 Multi_Lang Module Team.
* @license    http://dev.kohanaphp.com/wiki/multilang/License
*/
class url extends url_Core {
	/**
	* Fetches an absolute site URL based on a URI segment.
	*
	* @param   string  site URI to convert
	* @param   string  non-default protocol
	* @return  string
	*/
	public static function site($uri = '', $protocol = FALSE)
	{
		if ($path = trim(parse_url($uri, PHP_URL_PATH), '/'))
		{
			// Add path suffix
			$path .= Kohana::config('core.url_suffix');
		}
		
		if ($query = parse_url($uri, PHP_URL_QUERY))
		{
			// ?query=string
			$query = '?'.$query;
		}
		
		if ($fragment = parse_url($uri, PHP_URL_FRAGMENT))
		{
			// #fragment
			$fragment =  '#'.$fragment;
		}
		
		// Check if language is already in URL, else add the current language.
		
		$lang = '';
		
		if (Kohana::config('multi_lang.enabled'))
		{
			if ( ! preg_match('~^[a-z]{2}(?=/|$)~i', $path))
			{
				$lang = Router::$language.'/';
			}
		}
		
		// Concat the URL
		return url::base(TRUE, $protocol).$lang.$path.$query.$fragment;
	}
	
	/**
	* Fetches an absolute site URL based on a URI segment and supplied language.
	*
	* @param   string  language code to use. eg en/fr/es etc
	* @param   string  site URI to convert
	* @param   string  non-default protocol
	* @return  string
	*/
	public static function site_lang($lang, $uri = '', $protocol = FALSE)
	{
		if ($path = trim(parse_url($uri, PHP_URL_PATH), '/'))
		{
			// Add path suffix
			$path .= Kohana::config('core.url_suffix');
		}
		
		if ($query = parse_url($uri, PHP_URL_QUERY))
		{
			// ?query=string
			$query = '?'.$query;
		}
		
		if ($fragment = parse_url($uri, PHP_URL_FRAGMENT))
		{
			// #fragment
			$fragment =  '#'.$fragment;
		}
		
		if (Kohana::config('multi_lang.enabled')) {
			$lang .= '/';
		} else {
			// Wipe out the lang as the url will be invalid without multi_lang enabled.
			$lang = '';
		}
		
		// Concat the URL
		return url::base(TRUE, $protocol).$lang.$path.$query.$fragment;
	}
	
	/**
	* Fetches the current URI.
	*
	* @param   boolean  include the query string
	* @return  string
	*/
	public static function current($qs = FALSE)
	{
		$lang = (Kohana::config('multi_lang.enabled')) ? Router::$language.'/' : '';
		
		return ($qs === TRUE) ? $lang.Router::$complete_uri : $lang.Router::$current_uri;
	}
	
	/**
	* Fetches the current URI using a supplied language.
	*
	* @param   string	language code to use. eg en/fr/es etc
	* @param   boolean	include the query string
	* @return  string
	*/
	public static function current_lang($lang, $qs = FALSE)
	{		
		if (Kohana::config('multi_lang.enabled'))
		{
			$lang .= '/';
		}
		else
		{
			// Wipe out the lang as the url will be invalid without multi_lang enabled.
			$lang = '';
		}
		
		return ($qs === TRUE) ? $lang.Router::$complete_uri: $lang.Router::$current_uri;
	}
} // End url
