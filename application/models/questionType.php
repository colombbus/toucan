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

class QuestionType_Model extends ORM {

    const CHOICE = 1;
    const MULTIPLE_CHOICE = 2;
    const INTEGER = 3;
    const REAL = 4;
    const TEXT = 5;
    const LONG_TEXT = 6;
    const SEPARATOR = 7;
    const SUB_SEPARATOR = 8;

    const NO_TYPE = 0;

    protected $table_name = "question_types";

    public function getTranslatedName() {
        if ($this->loaded) {
            return Kohana::lang('question.'.$this->name);
        }
        return "";
    }

    public static function getTypesWithChoices() {
        $types = ORM::factory('questionType');
        $types->where('choices','1');
        return $types->find_all();
    }

    public static function getTypesWithChoicesIds() {
        $types = self::getTypesWithChoices();
        $result = array();
        foreach ($types as $type) {
            $result[] = $type->id;
        }
        return $result;
    }

    public static function getTranslatedTypes() {
        $types = self::factory('questionType');
        $types = $types->orderby('order')->find_all();
        $result = array();
        foreach ($types as $type) {
            $result[$type->id] = $type->getTranslatedName();
        }
        return $result;
    }


}
?>