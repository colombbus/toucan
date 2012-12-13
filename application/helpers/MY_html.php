<?php defined('SYSPATH') or die('No direct script access.');
 /**
 * Toucan is a web application to perform evaluation and follow-up of
 * activities.
 * Copyright (C) 2010 Colombbus (http://www.colombbus.org)
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class html extends html_Core {
 
	public function __construct()
	{
		// don't for get to call the parent constructor!
		parent::__construct();
	}
	
	public static function url($uri){
		if ($uri === '')
		{
			$site_url = url::base(FALSE);
		}
		elseif (strpos($uri, '://') === FALSE AND strpos($uri, '#') !== 0)
		{
			$site_url = url::site($uri);
		}
		else
		{
			$site_url = $uri;
		}
		return $site_url;
	}	
}
?>