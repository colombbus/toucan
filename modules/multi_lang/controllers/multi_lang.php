<?php
/**
 * Test/Demo Controller for Multi_Lang Module
 *
 * @package    Multi_Lang Module 
 * @author     Kiall Mac Innes
 * @copyright  (c) 2007-2009 Multi_Lang Module Team..
 * @license    http://dev.kohanaphp.com/wiki/multilang/License
 */
class Multi_Lang_Controller extends Controller {
	// Disable this controller when Kohana is set to production mode.
	// See http://docs.kohanaphp.com/installation/deployment for more details.
	const ALLOW_PRODUCTION = FALSE;

	public function index()
	{
		echo "<h1>Simple Multi Lang Example</h1>";
		
		echo "<p><b>To report problems/ask for help/submit a patch/... Visit <a href='http://projects.kohanaphp.com/projects/multilang'>http://projects.kohanaphp.com/projects/multilang</a></b></p>";
		
		echo "<br/><br/>";
		
		if (Kohana::config('multi_lang.enabled'))
		{
			echo "<h2>Multi Lang Module IS enabled.</h2>";
		}
		else
		{
			echo "<h2>Multi Lang Modle IS NOT enabled.</h2>";
		}
		
		echo "<p><b>url::current()</b> ".url::current()."</p>";
		echo "<p><b>url::current(true)</b> ".url::current(true)."</p>";
		
		echo "<br/><br/>";
		
		echo "<p><b>url::current_lang('fr')</b> ".url::current_lang('fr')."</p>";
		echo "<p><b>url::current_lang('fr', true)</b> ".url::current_lang('fr', true)."</p>";
		
		echo "<br/><br/>";
		
		echo "<p><b>url::base()</b> ".url::base()."</p>";
		echo "<p><b>url::base(true,'https')</b> ".url::base(true,'https')."</p>";
		echo "<p><b>url::base(false,'https')</b> ".url::base(false,'https')."</p>";
		
		echo "<br/><br/>";
		
		echo "<p><b>url::site()</b> ".url::site()."</p>";
		echo "<p><b>url::site('test')</b> ".url::site('test')."</p>";
		echo "<p><b>url::site('test','https')</b> ".url::site('test','https')."</p>";
		
		echo "<br/><br/>";
		
		echo "<p><b>url::site_lang('fr')</b> ".url::site_lang('fr')."</p>";
		echo "<p><b>url::site_lang('fr', 'test')</b> ".url::site_lang('fr', 'test')."</p>";
		echo "<p><b>url::site_lang('fr', 'test', 'https')</b> ".url::site_lang('fr', 'test', 'https')."</p>";
		
		echo "<br/><br/>";
		
		echo "<p><b>url::file('file.zip')</b> ".url::file('file.zip')."</p>";
		echo "<p><b>url::file('file.zip',true)</b> ".url::file('file.zip',true)."</p>";
		
		echo "<br/><br/>";
		
		echo "<h2>Router class variables:</h2>";
		
		echo "<p><b>Router::\$current_uri</b> ".Router::$current_uri."</p>";
		echo "<p><b>Router::\$query_string</b> ".Router::$query_string."</p>";
		echo "<p><b>Router::\$complete_uri</b> ".Router::$complete_uri."</p>";
		echo "<p><b>Router::\$routed_uri</b> ".Router::$routed_uri."</p>";
		echo "<p><b>Router::\$url_suffix</b> ".Router::$url_suffix."</p>";
		
		echo "<br/><br/>";
		
		echo "<p><b>Router::\$segments</b> ".print_r(Router::$segments)."</p>";
		echo "<p><b>Router::\$rsegments</b> ".print_r(Router::$rsegments)."</p>";
		
		echo "<br/><br/>";
		
		echo "<p><b>Router::\$controller</b> ".Router::$controller."</p>";
		echo "<p><b>Router::\$controller_path</b> ".Router::$controller_path."</p>";
		
		echo "<br/><br/>";
		
		echo "<p><b>Router::\$method</b> ".Router::$method."</p>";
		echo "<p><b>Router::\$arguments</b> ".print_r(Router::$arguments)."</p>";
	}
} // End Multi_Lang Controller 
