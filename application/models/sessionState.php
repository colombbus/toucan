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

class SessionState_Model extends ORM {

    protected $table_name = "session_states";
    protected $ignored_columns = array('translatedName');
    const UNDER_CONSTRUCTION = 1;
    const GOING_ON = 2;
    const OVER = 4;
    const CANCELLED = 5;


    public function getTranslatedName() {
        if ($this->loaded) {
            return Kohana::lang('session.'.$this->name);
        }
        return "";
    }

    public static function getTranslatedStates() {
        $states = self::factory('sessionState');
        $states = $states->find_all();
        $result = array();
        foreach ($states as $state) {
            $result[$state->id] = $state->getTranslatedName();
        }
        return $result;
    }

    public function load_values(array $values) {
        parent::load_values($values);
        $this->object['translatedName'] = $this->getTranslatedName();
        return $this;
    }


}
?>