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

class FormCopy_Model extends Copy_Model {

    protected $sessionName = "FormSession";
    protected $templateName = "FormTemplate";
    protected $copyName = "formCopy";
	protected static $conditionals = array();
    protected $ignored_columns = array('conditionals');
    protected static $requiredState;

    public function getCreationData($access, & $user, & $parameters = null) {
        $creationData = array();
        if (isset($parameters)) {
            $public_access = (isset($parameters['public_access'])&&$parameters['public_access']);
            if (isset($parameters['session_id'])) {
                $sessionId = $parameters['session_id'];
                if ($public_access)
                    $questions = $this->getQuestions($sessionId);
                else 
                    $questions = $this->getQuestions($sessionId, $user);
            } else if (isset($parameters['template_id'])) {
                $template = ORM::factory($this->templateName, $parameters['template_id']);
                if ($public_access)
                    $questions = $template->getQuestions(false);
                else
                    $questions = $template->getQuestions(true);
            }
            self::$requiredState = false;
            foreach ($questions as $question) {
                $item = array();
                if ($question->private)
                    $item['class'] = 'edit_copy_private';
                else
                    $item['class'] = 'edit_copy';
                $item['translated_label'] = $question->text;
                $item['convert_urls'] = true;
                if (strlen(trim($question->description))>0)
                    $item['translated_description'] = $question->description;
                $item['required'] = $question->required;
                if ($item['required'])
                    self::$requiredState = true;
                $item['name'] = 'question_'.$question->id;
                $item['id'] = 'question_'.$question->id;
                switch ($question->type_id) {
                    case QuestionType_Model::CHOICE : // Radio choice
                        $item['type'] = 'choice';
                        foreach ($question->choices as $choice) {
                            $item['values'][] = array('value'=>$choice->id, 'label'=>$choice->text, 'id'=>'question_'.$question->id.'_'.$choice->id);
                        }
                        if (isset($this->values[$question->id])) {
                            $item['value'] = $this->values[$question->id];
                        }
                        break;
                    case QuestionType_Model::MULTIPLE_CHOICE : // Checkboxes choices
                        $item['type'] = 'multiple_choice';
                        if (isset($this->values[$question->id]))
                            $checked = $this->values[$question->id];
                        else
                            $checked = array();
                        foreach ($question->choices as $choice) {
                            if (in_array($choice->id, $checked ))
                                $item['values'][] = array('name'=>'question_'.$question->id.'[]', 'value'=>$choice->id, 'id'=>'question_'.$question->id.'_'.$choice->id, 'value'=>$choice->id, 'label'=>$choice->text, 'checked' => 1);
                            else
                                $item['values'][] = array('name'=>'question_'.$question->id.'[]', 'value'=>$choice->id, 'id'=>'question_'.$question->id.'_'.$choice->id, 'value'=>$choice->id, 'label'=>$choice->text);
                        }
                        break;
                    case QuestionType_Model::INTEGER : // Integer value
                    case QuestionType_Model::REAL : // Real value
                    case QuestionType_Model::TEXT : // Short text
                        $item['type'] = 'text';
                        if (isset($this->values[$question->id])) {
                            $item['value'] = $this->values[$question->id];
                        }
                        break;
                    case QuestionType_Model::LONG_TEXT : // Long text
                        $item['type'] = 'long_text';
                        if (isset($this->values[$question->id])) {
                            $item['value'] = $this->values[$question->id];
                        }
                        break;
                    case QuestionType_Model::SEPARATOR : // separator
                    case QuestionType_Model::SUB_SEPARATOR : // separator
                        $item['type'] = 'form_separator';
                        $item['sub_separator'] = $question->isSubSeparator();
                        $item['value'] = $question->text;
                        unset ($item['class']);
                        break;
                }
                // triggers
                if ($question->hasTriggers()) {
                    $triggers = $question->getTriggers();
                    if ($triggers->valid()) {
                        $hide = true;
                        $questionId = $triggers->current()->question_id;
                        if (isset($this->values[$questionId])) {
                            $currentValue = $this->values[$questionId];
                        } else {
                            $currentValue = null;
                        }
                        foreach ($triggers as $trigger) {
                            self::$conditionals[$trigger->question_id][$question->id][] = $trigger->id;
                            if (isset ($currentValue)) {
                                if (is_array($currentValue)) {
                                    if (in_array($trigger->id, $currentValue ))
                                        $hide = false;
                                } else {
                                    if ($trigger->id == $currentValue)
                                        $hide = false;
                                }
                            }
                        }
                        if ($hide) {
                            $item['hidden']=true;
                        }
                    }
                }
                $creationData[] = $item;
            }
        }
        $creationData[] = array('type'=>'hidden', 'name'=>'state_id', 'id'=>'state_id', 'value'=>CopyState_Model::PUBLISHED);
        return $creationData;
    }

    public function getDisplayableData($access, & $user = null) {
        $displayableData = array();
        $displayableData[] = array('type'=>'text','label'=>'formCopy.owner','value'=>$this->owner_name);
        $displayableData[] = array('type'=>'text','label'=>'formCopy.creation_date','value'=>Utils::translateTimestamp($this->created));
        $displayableData[] = array('type'=>'text','label'=>'formCopy.state','value'=>$this->state->translatedName);
        $displayableData[] = array('type'=>'separator');
        $questions = $this->getQuestions(null, $user);
        foreach ($questions as $question) {
            $answers = $this->getAnswers($question->id);
            $item = array();
            $item['class'] = 'view_copy';
            $item['translated_label'] = $question->text;//.Kohana::lang('copy.colons');
            switch ($question->type_id) {
                case QuestionType_Model::CHOICE : // Radio choice
                    $item['type'] = 'text';
                    if ($answers->valid()) {
                        $answer = $answers->current();
                        $choice = ORM::factory('choice', $answer->choice_id);
                        $item['value'] = $choice->text;
                    } else {
                        $item['value'] = "";
                    }
                    break;
                case QuestionType_Model::MULTIPLE_CHOICE : // Checkboxes choices
                    $item['type'] = 'list';
                    $values = array();
                    if ($answers->valid()) {
                        foreach ($answers as $answer) {
                            $choice = ORM::factory('choice', $answer->choice_id);
                            $values[][] = array('type'=>'text', 'value'=>$choice->text);
                        }
                    }
                    $item['values'] = $values;
                    break;
                case QuestionType_Model::INTEGER : // Integer value
                case QuestionType_Model::REAL : // Real value
                case QuestionType_Model::TEXT : // Short text
                    $item['type'] = 'text';
                    if ($answers->valid()) {
                        $answer = $answers->current();
                        $value = ORM::factory('shortValue', $answer->value_id);
                        $item['value'] = $value->value;
                    } else
                        $item['value'] = "";
                    break;
                case QuestionType_Model::LONG_TEXT : // Long text
                    $item['type'] = 'long_text';
                    if ($answers->valid()) {
                        $answer = $answers->current();
                        $value = ORM::factory('longValue', $answer->value_id);
                        $item['value'] = $value->value;
                    } else
                        $item['value'] = "";
                    break;
                    
                case QuestionType_Model::SEPARATOR : // separator
                case QuestionType_Model::SUB_SEPARATOR : // separator
                    $item['type'] = 'form_separator';
                    $item['sub_separator'] = $question->isSubSeparator();
                    $item['value'] = $question->text;
                    unset ($item['class']);
                    break;
            }
            $displayableData[] = $item;
        }
        return $displayableData;
    }

    public function getEditableData($access, & $user, $name="", $description="") {
        $questions = $this->getQuestions(null, $user);
        if (!$this->valuesSetFromPost) {
            // Read values from database
            $this->values = array();
            foreach ($questions as $question) {
                $answers = $this->getAnswers($question->id);
                if ($answers->valid()) {
                    switch ($question->type_id) {
                        case QuestionType_Model::CHOICE : // Radio choice
                            $answer = $answers->current();
                            $this->values[$question->id] = $answer->choice_id;
                            break;
                        case QuestionType_Model::MULTIPLE_CHOICE : // Checkboxes choices
                            $values = array();
                            foreach ($answers as $answer) {
                                $values[] = $answer->choice_id;
                            }
                            $this->values[$question->id] = $values;
                            break;
                        case QuestionType_Model::INTEGER : // Integer value
                        case QuestionType_Model::REAL : // Real value
                        case QuestionType_Model::TEXT : // Short text
                            $answer = $answers->current();
                            $value = ORM::factory('shortValue', $answer->value_id);
                            $this->values[$question->id] = $value->value;
                            break;
                        case QuestionType_Model::LONG_TEXT : // Long text
                            $answer = $answers->current();
                            $value = ORM::factory('longValue', $answer->value_id);
                            $this->values[$question->id] = $value->value;
                            break;
                    }
                }
            }
        }
        $parameters = array('session_id'=>$this->session_id);
        $data = $this->getCreationData($access, $user, $parameters);
        return $data;
    }

    protected function validateAnswers(array & $array, & $user, $sessionId = null) {
        if ((isset($array['state_id']))&&($array['state_id']!= CopyState_Model::PUBLISHED))
            $partialValidation = true;
        else
            $partialValidation = false;

        $questions = $this->getQuestions($sessionId, $user);
        $this->values = array();
        foreach ($questions as $question) {
            if (isset($array['question_'.$question->id])) {
                if (!is_array($array['question_'.$question->id])&&strlen(trim($array['question_'.$question->id]))==0) {
                    unset ($array['question_'.$question->id]);
                }
            }
            if (isset($array['question_'.$question->id])) {
                $value = $array['question_'.$question->id];
                switch ($question->type_id) {
                    case QuestionType_Model::CHOICE : // Radio choice
                        $choice = ORM::factory('choice', $value);
                        if ($choice->question_id == $question->id) {
                           $this->values[$question->id] = $value;
                        } else {
                            // add error
                            $this->errors['question_'.$question->id] = "unknown_choice";
                        }
                        break;
                    case QuestionType_Model::MULTIPLE_CHOICE : // Checkboxes choices
                        if (! is_array($value)) {
                            // add error
                            $this->errors['question_'.$question->id] = "incorrect_value";
                        } else {
                            $answers = array();
                            $answersOk = false;
                            foreach ($value as $item) {
                                $choice = ORM::factory('choice', $item);
                                if ($choice->question_id == $question->id) {
                                    $answers[] = $item;
                                } else {
                                    // add error
                                    $this->errors['question_'.$question->id] = "unknown_choice";
                                    break;
                                }
                            }
                            if (!isset ($this->errors['question_'.$question->id])) {
                                $this->values[$question->id] = $answers;
                            }
                        }
                        break;
                    case QuestionType_Model::INTEGER : // Integer value
                        if (!$this->is_integer($value)) {
                            $this->errors['question_'.$question->id] = "wrong_integer";
                        } else {
                            $this->values[$question->id] = $value;
                        }
                        break;
                    case QuestionType_Model::REAL : // Real value
                        if (!is_numeric($value)) {
                            $this->errors['question_'.$question->id] = "wrong_real";
                        } else {
                            $this->values[$question->id] = $value;
                        }
                        break;
                    case QuestionType_Model::TEXT : // Short text
                        if (strlen($value)>self::SHORT_VALUE_MAX_LENGTH) {
                            $this->errors['question_'.$question->id] = "short_value_too_long";
                        } else {
                            $this->values[$question->id] = $value;
                        }
                        break;
                    case QuestionType_Model::LONG_TEXT : // Long text
                        if (strlen($value)>self::LONG_VALUE_MAX_LENGTH) {
                            $this->errors['question_'.$question->id] = "long_value_too_long";
                        } else {
                            $this->values[$question->id] = $value;
                        }
                        break;
                }
            } else {
                if (($question->required)&&(!$partialValidation)) {
                    if ($question->hasTriggers()) {
                        // question may or may not be visible: we check if it is visible
                        $triggersIds = $question->getTriggersIds();
                        $firstTrigger = ORM::factory('choice', $triggersIds[0]);
                        if (isset($array['question_'.$firstTrigger->question_id])) {
                            $triggersQuestionValue = $array['question_'.$firstTrigger->question_id];
                            $errorDetected = false;
                            foreach($triggersIds as $triggerId) {
                                if (is_array($triggersQuestionValue)) {
                                    foreach ($triggersQuestionValue as $option) {
                                        if (intval($option)==$triggerId) {
                                            $errorDetected = true;
                                            break;
                                        }
                                    }
                                } else if (intval($triggersQuestionValue)==$triggerId) {
                                    $errorDetected = true;
                                }
                                if ($errorDetected) {
                                    // question is visible: add error
                                    $this->errors['question_'.$question->id] = "value_required";
                                    break;
                                }
                            }
                        }
                    } else {
                        // add error
                        $this->errors['question_'.$question->id] = "value_required";
                    }
                } else {
                    switch ($question->type_id) {
                        case QuestionType_Model::TEXT :
                        case QuestionType_Model::LONG_TEXT :
                        case QuestionType_Model::REAL :
                        case QuestionType_Model::INTEGER :
                            // set the value empty
                            $this->values[$question->id] = '';
                            break;
                    }
                }
            }
        }
    }

    protected function saveAnswers() {
        foreach ($this->values as $id=>$value) {
            $question = ORM::factory('question', $id);
            switch($question->type_id) {
                case QuestionType_Model::CHOICE : // Radio choice
                    $answer = $this->getAnswer($id);
                    $answer->choice_id = $value;
                    $answer->save();
                    break;
                case QuestionType_Model::MULTIPLE_CHOICE : // Checkboxes choices
                    $goodIds = array();
                    foreach ($value as $item) {
                        $answer = $this->getAnswer($id, $item);
                        $answer->save();
                        $goodIds[] = $item;
                    }
                    $this->deleteOtherAnswers($id, $goodIds);
                    break;
                case QuestionType_Model::INTEGER : // Integer value
                case QuestionType_Model::REAL : // Real value
                case QuestionType_Model::TEXT : // Short text
                    $answer = $this->getAnswer($id);
                    if ($answer->loaded) {
                        $shortValue = ORM::factory('shortValue', $answer->value_id);
                        $saveRequired = false;
                    } else {
                        $shortValue = ORM::factory('shortValue');
                        $saveRequired = true;
                    }
                    $shortValue->value = $value;
                    $shortValue->save();
                    if ($saveRequired) {
                        $answer->value_id = $shortValue->id;
                        $answer->save();
                    }
                    break;
                case QuestionType_Model::LONG_TEXT : // Long text
                    $answer = $this->getAnswer($id);
                    if ($answer->loaded) {
                        $longValue = ORM::factory('longValue', $answer->value_id);
                        $saveRequired = false;
                    } else {
                        $longValue = ORM::factory('longValue');
                        $saveRequired = true;
                    }
                    $longValue->value = $value;
                    $longValue->save();
                    if ($saveRequired) {
                        $answer->value_id = $longValue->id;
                        $answer->save();
                    }
                    break;
            }
        }
    }

    protected function deleteOtherAnswers($questionId, $choiceIds) {
        $answers  = ORM::factory('answer')->where('question_id', $questionId)->where('copy_id', $this->id)->notin('choice_id', $choiceIds);
        $answers->delete_all();
    }

    protected function is_integer($value) {
        if (is_int($value)) {
            return true;
        } else if (is_string($value)&& is_numeric($value)) {
            return (strpos($value, '.') === false);
        }
        return false;
    }
	
	public function getConditionals() {
        return self::$conditionals;
    }

    public function getRequiredState() {
        return self::$requiredState;
    }
    

}
?>