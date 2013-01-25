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

class Variable_Model extends ORM {

    protected $has_one = array("question");
    protected $belongs_to = array("template");

    public function getValue(& $copy, $forceTextValue = false) {
        if ($this->question->id>0) {
            $answers = ORM::factory('answer')->where('copy_id', $copy->id)->where('question_id', $this->question->id)->find_all();
            if (!$answers->valid()) {
                return null;
            }
            switch ($this->question->type_id) {
                case QuestionType_Model::CHOICE :
                    $answer = $answers->current();
                    return $answer->getValue($forceTextValue);
                    break;
                case QuestionType_Model::MULTIPLE_CHOICE :
                    $values = array();
                    foreach ($answers as $answer) {
                        $values[] = $answer->getValue($forceTextValue);
                    }
                    return $values;
                    break;
                default :
                    $answer = $answers->current();
                    $value = $answer->getValue($forceTextValue);
                    if (strlen(trim($value))==0)
                        return null;
                    else
                        return $value;
                    break;
            }
        } else {
            // TO BE IMPLEMENTED
        }
    }
    
    public function getChoiceIds(& $copy) {
        if ($this->question->id>0) {
            $answers = ORM::factory('answer')->where('copy_id', $copy->id)->where('question_id', $this->question->id)->find_all();
            if (!$answers->valid()) {
                return null;
            }
            $ids = array();
            foreach($answers as $answer) {
                $ids[] = $answer->choice_id;
            }
            return $ids;
        }
        return null;
    }

    public function copy($variable, $question) {
        $this->name = $variable->name;
        $this->template_id = $question->template_id;
        $this->numerical = $variable->numerical;
        $this->save();
    }
    
    public function isMultiple() {
        if (!$this->loaded)
            return false;
        return ($this->question->type_id == QuestionType_Model::MULTIPLE_CHOICE);
    }
}
?>