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

class SurveyCategory_Model extends Category_Model {

    protected $accessParent = "survey";
    protected $parentId = "session_id";
    protected $controllerName = "surveyCategory";

 
    public function __get($column) {
        if ($column == 'survey') {
            if ($this->session_id >0) {
                return ORM::factory("survey", $this->session_id);
            }
            return null;
        }
        return parent::__get($column);
    }

    
    public function getPublicUrl() {
        if ($this->isPublished()) {
            return "publicSurvey/indicators/".$this->id;
        }
    }

    
}
?>