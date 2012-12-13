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

class Answer_Model extends ORM {

    protected $belongs_to = array('copy');
    protected $has_one = array('choice', 'question');

    public function delete() {
        $value = $this->value;
        if (isset($value))
            $value->delete();
        parent::delete();
    }

    public function __get($column) {
        if ($column == 'copy') {
            // retrieve a formCopy or an InterviewCopy
            if (isset($this->copy_id)) {
                $result = $this->db->query("SELECT session.type from sessions,copies WHERE copies.id = $this->copy_id and copies.session_id = sessions.id");
                $result->result(false,MYSQL_NUM);
                switch ($result[0][0]) {
                    case 1:
                        return new FormCopy_Model($this->copy_id);
                        break;
                    case 2:
                        return new InterviewCopy_Model($this->copy_id);
                        break;
                }
            }
            return null;
        } else if ($column == 'value') {
            $question = $this->question;
            $template = $question->template;
            switch ($template->type) {
                case Template_Model::FORM_TYPE:
                    switch ($this->question->type_id) {
                        case QuestionType_Model::INTEGER : // Integer value
                        case QuestionType_Model::REAL : // Real value
                        case QuestionType_Model::TEXT : // Short text
                            return ORM::factory('shortValue', $this->value_id);
                            break;
                        case QuestionType_Model::LONG_TEXT : // Long text
                            return ORM::factory('longValue', $this->value_id);
                            break;
                    }
                    break;
                case Template_Model::INTERVIEW_TYPE:
                    return ORM::factory('textValue', $this->value_id);
                    break;
            }
            return null;
        }
        return parent::__get($column);
    }

    public function getValue($forceTextValue = false) {
        if ($this->loaded) {
            switch ($this->question->type_id) {
                case QuestionType_Model::CHOICE :
                case QuestionType_Model::MULTIPLE_CHOICE :
                    $value = $this->choice->value;
                    if ($forceTextValue||(strlen(trim($value))==0))
                        return $this->choice->text;
                    return $value;
                    break;
                default:
                    return $this->value->value;
                    break;
            }
        }
        return null;
    }


}
?>