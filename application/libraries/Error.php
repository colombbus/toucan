<?php  defined('SYSPATH') OR die('No direct access allowed.');
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

class Error_Core {

    private $session;

    public function __construct() {
        $this->session = Session::instance();
    }

    public static function factory() {
        return new Error();
    }

    public static function instance() {
        static $instance;
        empty($instance) and $instance = new Error();
        return $instance;
    }

    public function addError($errorText, $key='main') {
        $errors = $this->getErrors();
        if ($errors) {
            if ($key)
               $errors[$key] = $errorText;
            else
               $errors[] = $errorText;
        } else {
            if ($key)
                $errors = array($key => $errorText);
            else
                $errors = array($errorText);
        }
        $this->session->set_flash('errors',$errors);
    }

    public function getErrors() {
        return $this->session->get('errors');
    }

    public function getAllInfo() {
        return $this->session->get('errors_info');
    }
    
    public function hasErrors() {
        $errors = $this->getErrors();
        if (($errors)&&(sizeof($errors)>=0)) {
            return true;
        }
        return false;
    }

    public function hasError($key) {
        $errors = $this->getErrors();
        if ($errors&&isset($errors[$key])){
            return true;
        }
        return false;
    }

    public function getErrorMessage($key = NULL){
        $errors = $this->getErrors();
        if ($key===NULL) {
            if (($errors)&&(sizeof($errors)>=0))
                $name =$errors[0];
            else
                return ''; // There is no error
        } else {
            if ($errors&&isset($errors[$key]))
                $name =$errors[$key];
            else
                return ''; // There is no error
        }
        return Kohana::lang('error.'.$name);
    }

    public function clearErrors() {
        $this->session->clear('errors');
        $this->session->clear('errors_info');
    }

    public function addInfo($key, $value) {
        $info = $this->getAllInfo();
        if ($info) {
           $info[$key] = $value;
        } else {
            $info = array($key => $value);
        }
        $this->session->set_flash('errors_info',$info);
    }
    
    public function getInfo($key) {
        $info = $this->getAllInfo();
        if ($info && isset($info[$key])) {
            return $info[$key];
        }
        return null;
    }
    
}
?>