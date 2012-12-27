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

class Selection_Model extends ORM {

    public function getTranslatedName() {
        return Kohana::lang("selection.$this->name");
    }

    public static function getTranslatedList($simple = null) {
        if (isset($simple)) {
            if ($simple) {
                $selections = self::factory('selection')->where('simple', '1')->find_all();
            } else {
                $selections = self::factory('selection')->where('multiple', '1')->find_all();
            }
        } else {
            $selections = self::factory('selection')->find_all();
        }
        $result = array();
        foreach ($selections as $selection) {
            $result[$selection->id] = $selection->getTranslatedName();
        }
        return $result;
    }

    public static function getIdsWithValue() {
        $selections = self::factory('selection')->find_all();
        $result = array();
        foreach ($selections as $selection) {
            if ($selection->requires_value)
                $result[] = $selection->id;
        }
        return $result;
    }

    public static function getIdsMultipleOnly() {
        $selections = self::factory('selection')->find_all();
        $result = array();
        foreach ($selections as $selection) {
            if ($selection->multiple && !$selection->simple)
                $result[] = $selection->id;
        }
        return $result;
    }

    public static function getIdsSimpleOnly() {
        $selections = self::factory('selection')->find_all();
        $result = array();
        foreach ($selections as $selection) {
            if ($selection->simple && !$selection->multiple)
                $result[] = $selection->id;
        }
        return $result;
    }

    public function __get($column) {
        if ($column == 'translatedName') {
            return $this->getTranslatedName();
        }
        return parent::__get($column);
    }

}
?>