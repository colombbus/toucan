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

class SurveyIndicator_Model extends Indicator_Model implements Ajax_Model {

    protected $indicatorModel = "surveyIndicator";
    protected $table_name = "indicators";
    protected $accessParent = "survey";
    protected $parentName = "survey";
    protected $parentId = "session_id";
    protected $displaySessionLink = false;
    protected $controllerName = "surveyIndicator";
    protected $actual_object_name = "indicator";


    public function __get($column) {
        if ($column == 'survey') {
            return ORM::factory('survey', $this->session_id);
        }
        return parent::__get($column);
    }
    
    public function getNextOrder($surveyId) {
        $result = $this->db->query("SELECT max(`order`)+1 from indicators WHERE evaluation_id = '0' AND session_id = $surveyId");
        $result->result(false,MYSQL_NUM);
        if ($result[0][0] === null)
            return 1;
        else
            return $result[0][0];
    }

    public function uniqueNameByParent(Validation $valid) {
        if (array_key_exists('name', $valid->errors()))
            return;
        if (isset ($valid->session_id)) {
            $escapedName = addslashes($valid->name);
            $surveyId = $valid->session_id;
            if ($this->loaded) {
                $other = $this->db->query("SELECT id from ".$this->table_name." WHERE id != $this->id AND evaluation_id = '0' AND session_id = '$surveyId' AND name = '$escapedName'");
            } else {
                $other = $this->db->query("SELECT id from ".$this->table_name." WHERE evaluation_id = '0' AND session_id = '$surveyId' AND name = '$escapedName'");
            }
            if ($other->count() > 0) {
                $valid->add_error( 'name', 'uniqueName');
            }
        }
    }

    protected function getSessions() {
        return array($this->survey);
    }
    
    
}
?>