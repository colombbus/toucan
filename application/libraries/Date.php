<?php defined('SYSPATH') OR die('No direct access allowed.');
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

class Date_Core  {
	var $date;
	
	public function __construct($dat=NULL)
	{
		if($dat!=NULL){
			if($this->isValid($date)){
				$this->date=$dat;
				return true;
			}
			else return false;
		}
		else return true;
	}

	public static function factory()
	{
		return new Date();
	}

	public static function instance()
	{
		static $instance;

		// Load the Auth instance
		empty($instance) and $instance = new Date();

		return $instance;
	}
	
	public static function getDate(){
		$aux=getdate();
		return $aux['year']."-".$aux['mon']."-".$aux['mday'];
	}
	
	public static function isValid($date)
    {
        $date = str_replace(array('\'', '-', '.', ','), '/', $date);
        $date = explode('/', $date);

/*par raison de comodité on n'admet pas le format timestamp unix */        //if(count($date) == 1 && is_numeric($date[0]) && $date[0] < 20991231 &&(checkdate(substr($date[0], 4, 2), substr($date[0], 6, 2), substr($date[0], 0, 4))))  return true; 
        
        if(count($date) == 3 && is_numeric($date[0]) &&  is_numeric($date[1])&& is_numeric($date[2]) && ( checkdate($date[0], $date[1], $date[2]) /*mmddyyyy*/|| checkdate($date[1], $date[0], $date[2]) /*ddmmyyyy*/|| checkdate($date[1], $date[2], $date[0])) /*yyyymmdd*/) return true;

        return false;
    } 
    
	public function getMonth($dat=NULL){
		if($dat==NULL) $dat=$this->date;
		$ret=date_parse($dat);
        return $ret['month'];
	}
	
	public function getYear($dat=NULL){
		if($dat==NULL) $dat=$this->date;
		$ret=date_parse($dat);
        return $ret['year'];
	}
	
	public function getDay($dat=NULL){
		if($dat==NULL) $dat=$this->date;
		$ret=date_parse($dat);
        return $ret['day'];
	}
	
	public function Compare($date1,$date2){  //returns Unix timestamp diférence betwen two dates. 
		$date1 = str_replace(array('\'', '-', '.', ','), '/', $date1);
        $aux1 = explode('/', $date1);
        $date2 = str_replace(array('\'', '-', '.', ','), '/', $date2);
        $aux2 = explode('/', $date2);
        if(count($aux1)!=1) $date1=strtotime($date1);
        if(count($aux2)!=1) $date2=strtotime($date2);
        return $date2-$date1;
	}
	
}
?>