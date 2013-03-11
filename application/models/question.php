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

class Question_Model extends ToucanTree_Model implements Ajax_Model {

    protected $belongs_to = array('template', 'variable');
    protected $has_many = array('choices');
    protected $has_one = array('type'=>'questionType');
    protected $ignored_columns = array('choice', 'variable_name');
    protected $previousChoices = null;
    protected $variable_name = null;
    protected $choiceErrors = array();
    protected $ORM_Tree_children = "questions";


    public function getCreationData($access, & $user, & $parameters = null) {
        /*if (isset ($parameters) && isset($parameters['separator'])) {
            $this->type_id = QuestionType_Model::SEPARATOR;
        }*/
        return $this->getEditableData($access, $user);
    }

    protected function buildValidation(array & $array) {
        $this->validation = Validation::factory($array)
            ->pre_filter('trim')
            ->add_rules('text', 'required', 'length[1,250]')
            ->add_rules('description', 'length[0,500]')
            ->add_rules('template_id', 'required', 'valid::numeric')
            ->add_rules('type_id', 'required', 'valid::numeric')
            ->add_rules('sub_separator', 'in_array[0,1]');
        if ($this->isAdvanced()) {
            $this->validation->add_rules('required', 'in_array[0,1]');
            $this->validation->add_rules('variable_id', 'required', 'valid::numeric');
            $this->validation->add_rules('variable_name', 'length[0,50]', array($this, 'uniqueVariableName'));
        }
    }

    public function getEditableData($access, & $user) {
        $editableData = array();
        $editableData[] = array ('type' => 'text','name' => 'text','label' => 'question.text','required'=>'1', 'value' => $this->text);
        $editableData[] = array ('type' => 'long_text','name' => 'description','label' => 'question.description','value' => $this->description);
        if ($this->isSeparator()) {
            $editableData[] = array ('type' => 'check','name' => 'sub_separator','label' => 'question.sub_separator', 'value' => 1, 'checked' => $this->sub_separator);
        }
        if ($this->isAdvanced()) {
            if (!$this->isSeparator()) {
                $editableData[] = array ('type' => 'check','name' => 'required','label' => 'question.required', 'value' => 1, 'checked' => $this->required);
                if (isset($this->variable_name))
                    $variableName = $this->variable_name;
                else
                    $variableName = $this->variable->name;
                $editableData[] = array ('type' => 'text','name' => 'variable_name','label' => 'question.variable', 'value' => $variableName);
                $editableData[] = array ('type' => 'select','name' => 'type_id','label' => 'question.type','required'=>'1', 'values' => QuestionType_Model::getTranslatedTypes(), 'value' => $this->type->id);
                $editableData[] = array ('type' => 'hidden','name' => 'variable_id', 'value' => $this->variable_id);
            } else {
                $editableData[] = array ('type' => 'hidden','name' => 'required', 'value' => 0);
                $editableData[] = array ('type' => 'hidden','name' => 'variable_name', 'value' => '');
                $editableData[] = array ('type' => 'hidden','name' => 'variable_id', 'value' => 0);
                $editableData[] = array ('type' => 'hidden','name' => 'type_id', 'value' => $this->type_id);
            }
        } else {
            $editableData[] = array ('type' => 'hidden','name' => 'type_id', 'value' => $this->type_id);
        }
        $editableData[] = array ('type' => 'hidden','name' => 'template_id', 'value' => $this->template_id);
        $editableData[] = array ('type' => 'hidden','name' => 'order', 'value' => $this->order);
        $editableData[] = array ('type' => 'hidden','name' => 'private', 'value' => $this->private);
        return $editableData;
    }

    public function getTriggersEditableData($access, & $user) {
        $editableData = array();
        $questions = $this->getItemsWithChoices($this->id);
        $questionNames = array();
        $questionNames[0] = "";
        foreach ($questions as $question) {
            $questionNames[$question->id] = $question->order." - ".substr($question->text,0,40);
        }
        if ($this->isSeparator())
            $prefix = "separator";
        else 
            $prefix = "question";
        $editableData[] = array ('type' => 'check','name' => 'enabled', 'id' => "triggers_enable_$this->id",'label' => $prefix.'.trigger_enabled','values' => $questionNames, 'checked' => ($this->parent_id>0));
        $editableData[] = array ('type' => 'select','name' => 'parent_id','id' => "triggers_choice_$this->id",'label' => $prefix.'.trigger','values' => $questionNames, 'value' => $this->parent_id);
        $editableData[] = array ('type' => 'check','name' => 'private', 'id' => "private",'label' => $prefix.'.private','value' => $this->private, 'checked' => $this->private);
        if ($this->parent_id > 0) {
            $editableData = array_merge($editableData, $this->getTriggerChoicesEditableData($access, $user));
        }
        return $editableData;
    }

    public function getTriggerChoicesEditableData($access, &$user, $id = null) {
        $editableData = array();
        if (!isset($id))
            $id = $this->parent_id;
        if ($this->isSeparator())
            $prefix = "separator";
        else 
            $prefix = "question";
        $editableData[] = array ('type' => 'separator','name' => 'choices', 'label' => $prefix.'.trigger_choices');
        $trigger = self::factory('question', $id);
        $choices = $trigger->choices;
        if ($id == $this->parent_id) {
            $triggerIds = $this->getTriggersIds();
            foreach($choices as $choice) {
                $editableData[] = array ('type' => 'check','name' => "trigger_$choice->id",'translated_label' => $choice->text, 'value' => 1, 'checked' => in_array($choice->id, $triggerIds));
            }
        } else {
            foreach($choices as $choice) {
                $editableData[] = array ('type' => 'check','name' => "trigger_$choice->id",'translated_label' => $choice->text, 'value' => 1);
            }
        }
        return $editableData;
    }

    public function getEditableChoices($access, & $user) {
        $editableData = array();
        if (isset($this->previousChoices)) {
            // retrieve choices from previously set data
            $choice = ORM::factory("choice");
            $order = 0;
            foreach ($this->previousChoices as $item) {
                $choice->text = $item['text'];
                $choice->value = $item['value'];
                $choice->order = $order;
                $order++;
                $choiceData['data'] = $choice->getEditableData($access, $user);
                $choiceData['index'] = $item['index'];
                $editableData[] = $choiceData;
            }
        } else {
            // retrieve choices from database
            $choices = $this->choices;
            foreach ($choices as $choice) {
                $choiceData['data'] = $choice->getEditableData($access, $user);
                $choiceData['index'] = $choice->order;
                $editableData[] = $choiceData;
            }
        }
        return $editableData;
    }

    public function getCreationChoice($access, & $user, $index = null) {
        $creationData = array();
        if (!isset($index))
            $index = $this->choices->count();
        $choice = ORM::factory("choice");
        $creationData['data'] = $choice->getCreationData($access, $user, $index);
        $creationData['index'] = $index;
        return $creationData;
    }

    public function getDisplayableData($access, & $user = null) {
        $displayableData = array();
        /*if (strlen(trim($this->description))>0) {*/
            $displayableData[] = array ('type' => 'long_text','name' => 'description','label' => 'question.description','value' => $this->description);
        //}
        if ($this->isSeparator()) {
            if ($this->isSubSeparator()) {
                $value = Kohana::lang('question.yes');
            } else {
                $value = Kohana::lang('question.no');
            }
            $displayableData[] = array ('type' => 'text', 'label' => 'question.sub_separator', 'value'=> $value);
        }
        if ($this->isAdvanced()) {
            if (!$this->isSeparator()) {
                if ($this->hasTriggers()) {
                    //$trigger = self::factory('question', $this->parent_id);
                    $trigger = $this->parent;
                    $displayableData[] = array ('type' => 'text', 'label' => 'question.triggered_by', 'value'=> $trigger->text);
                }
                if ($this->required) {
                    $value = Kohana::lang('question.yes');
                } else {
                    $value = Kohana::lang('question.no');
                }
                $displayableData[] = array ('type' => 'text', 'label' => 'question.required', 'value'=> $value);
                $displayableData[] = array ('type' => 'text','name' => 'variable_name','label' => 'question.variable','value' => $this->variable->name);
                $type = $this->type;
                $displayableData[] = array ('type' => 'text','name' => 'type_id','label' => 'question.type','value' => $type->getTranslatedName());
                if ($type->choices) {
                    $choices = $this->choices;
                    $displayableChoices = array();
                    foreach ($choices as $choice) {
                        $displayableChoices[] = $choice->getDisplayableData($access);
                    }
                    $displayableData[] = array ('type' => 'list','name' => 'choices','label' => 'question.choices','values' => $displayableChoices);
                }
            }
        }
        return $displayableData;
    }

    public function validateEdition(array & $array,& $user, $save = FALSE) {
        // intialize booleans
        $this->checkBooleans($array);

        // get the choices from array
        $choices = $this->retrieveChoices($array);

        $this->buildValidation($array);
        if (parent::validate($this->validation, false)) {
            if ($this->isAdvanced()) {
                // Validate the choices if any
                $type = ORM::factory("questionType",$array['type_id']);
                if ($type->choices) {
                    $choice = ORM::factory('choice');
                    $result = true;
                    foreach ($choices as $data) {
                        $currentResult = $choice->validateEdition($data, $user, false);
                        if (!$currentResult) {
                            // We add an error for this choice
                            $this->choiceErrors["choice_".$data['index']] = Kohana::lang('form_errors.choice.default');
                        }
                        $result = $result & $currentResult;
                    }
                    if (!$result)
                        return false;
                }
            }
            if ($this->isSeparator()) {
                if (isset($array['sub_separator'])&&$array['sub_separator'] == 1) {
                    $this->type_id = QuestionType_Model::SUB_SEPARATOR;
                } else {
                    $this->type_id = QuestionType_Model::SEPARATOR;
                }
            }
            if ($save) {
                $this->save();
                if ($this->isAdvanced()&&!$this->isSeparator()) {
                    // deal with empty variables
                    if (!isset($array['variable_name'])||strlen(trim($array['variable_name']))==0) {
                        $variableName = 'variable_'.$this->id;
                        $suffix="";
                        $i = 0;
                        while(!$this->uniqueVariableName($variableName.$suffix)) {
                            $i++;
                            $suffix = "_".$i;
                        }
                        $variableName .= $suffix;
                    } else {
                        $variableName = $array['variable_name'];
                    }
                    if ($this->variable_id > 0 ) {
                        $variable = $this->variable;
                        $variable->name = $variableName;
                        $variable->save();
                    } else {
                        $variable = ORM::factory('variable');
                        $variable->name = $variableName;
                        $variable->template_id = $this->template_id;
                        $variable->save();
                        $this->variable_id = $variable->id;
                        $this->save();
                    }
                    // update the choices if any
                    if ($type->choices) {
                        $newChoicesCount = sizeof($choices);
                        $oldChoices = $this->choices;
                        $index = 0;
                        $numericalValue = 1;
                        foreach ($oldChoices as $choice) {
                            if ($newChoicesCount>0) {
                                $choice->text = $choices[$index]['text'];
                                $choice->value = $choices[$index]['value'];
                                $choice->order = $choices[$index]['index'];
                                $choice->save();
                                if (!is_numeric($choices[$index]['value'])) {
                                    $numericalValue = 0;
                                }
                            } else {
                                $choice->delete();
                            }
                            $newChoicesCount--;
                            $index++;
                        }
                        for ($i = 0; $i<$newChoicesCount;$i++) {
                            $choice = ORM::factory('choice');
                            $choice->text = $choices[$index]['text'];
                            $choice->value = $choices[$index]['value'];
                            $choice->question_id = $this->id;
                            $choice->order = $choices[$index]['index'];
                            $choice->save();
                            if (!is_numeric($choices[$index]['value'])) {
                                $numericalValue = 0;
                            }
                            $index++;
                        }
                    }
                    // Set variable type
                    $variable = $this->variable;
                    switch($this->type_id) {
                            case QuestionType_Model::INTEGER :
                            case QuestionType_Model::REAL :
                                $variable->numerical = 1;
                                break;
                            case QuestionType_Model::TEXT :
                            case QuestionType_Model::LONG_TEXT :
                                $variable->numerical = 0;
                                break;
                            case QuestionType_Model::CHOICE :
                            case QuestionType_Model::MULTIPLE_CHOICE :
                                $variable->numerical = $numericalValue;
                                break;
                    }
                    $variable->save();
                }
            }
            return true;
        }
        return false;
    }

    public function validateTriggers($array, $user, $save) {
        $this->removeTriggers($save);
        if (isset($array['private']))
            $this->private = 1;
        else
            $this->private = 0;
        if (isset($array['enabled'])&&isset($array['parent_id'])&&($array['parent_id']>0)) {
            // retrieve triggers ids
            $keyText = "trigger_";
            $triggersId = array();
            foreach ($array as $key=>$value) {
                if (substr($key, 0, strlen($keyText)) === $keyText) {
                    $id = substr($key, strlen($keyText));
                    $triggersId[] = $id;
                }
            }
            if (count($triggersId)==0) {
                // no choices have been selected: fire an error
                $this->validation = Validation::factory($array);
                $this->validation->add_error('triggers', 'empty_choices');
                return false;
            }
            $this->addTriggers($array['parent_id'], $triggersId, $save);
        }
        if ($save) 
            $this->save();
        return true;
    }

    public function setValues($array) {
        $this->checkBooleans($array);
        $choices = $this->retrieveChoices($array);
        if (sizeof($choices)>0) {
            $this->previousChoices = $choices;
        }
        $this->load_values($array);
        if ($this->isAdvanced()&&isset($array['variable_name'])) {
            $this->variable_name = $array['variable_name'];
        }
    }

    public function setTriggersValues($array) {
        if (isset($array['enabled'])&&isset($array['parent_id'])&&($array['parent_id']>0)) {
            $this->parent_id = $array['parent_id'];
        } else {
            $this->parent_id = 0;
        }
        if (isset($array['private']))
            $this->private = 1;
        else
            $this->private = 0;

    }

    public function getErrors($lang_file=null) {
        $errors = parent::getErrors($lang_file);
        return $errors + $this->choiceErrors;
    }

    public function validateCreation(array & $array,& $user, $save = FALSE) {
        return $this->validateEdition($array, $user, $save);
    }

    public function isViewableBy(& $user) {
        if (isset($this->template_id)) {
            $template = $this->template;
            return $template->isViewableBy($user);
        }
        return false;
    }

    public function isEditableBy(& $user) {
        if (isset($this->template_id)) {
            $template = $this->template;
            return $template->isEditableBy($user);
        }
        return false;
    }

    public static function getNewQuestion($templateId, $separator = false) {
        $question = ORM::factory('question');
        $question->template_id = $templateId;
        if ($separator)
            $question->type_id = QuestionType_Model::SEPARATOR;
        else if ($question->isAdvanced())
            $question->type_id = QuestionType_Model::TEXT;
        else
            $question->type_id = QuestionType_Model::NO_TYPE;
        $template = $question->template;
        $question->order = $template->getNextOrder();

        return $question;
    }


    public function isAdvanced() {
        if (isset ($this->template_id)) {
            $template = $this->template;
            return ($template->questionAdvanced());
        }
        return false;
    }

    public function isSeparator() {
        if (isset ($this->type_id)) {
            return ($this->type_id == QuestionType_Model::SEPARATOR ||$this->type_id == QuestionType_Model::SUB_SEPARATOR);
        }
        return false;
    }

    public function isSubSeparator() {
        if (isset ($this->type_id)) {
            return ($this->type_id == QuestionType_Model::SUB_SEPARATOR);
        }
        return false;
    }

    public function isPrivate() {
        if (isset($this->private))
            return $this->private;
        return false;
    }

    public function count(& $filter , & $user, $constraintId = null) {
        // not implemented
    }

    public function getItems(& $filter = null,$parentId = 0, & $user = null,$offset = 0, $number = null, $constraintId = null) {
        // not implemented
    }

    public function getItemsWithChoices($idDifferentFrom = 0) {
        $this->in('type_id', QuestionType_Model::getTypesWithChoicesIds());
        $this->notin('id', array($idDifferentFrom));
        $this->where('template_id', $this->template_id);
        $this->orderby(array('order'=>'ASC'));
        return $this->find_all();
    }

    public function __get($column) {
        if ($column == 'template') {
            // retrieve a formTemplate or an InterviewTemplate
            if (isset($this->template_id)) {
                $template_id = $this->template_id;
                $result = $this->db->query("SELECT type from templates WHERE id = $template_id");
                $result->result(false,MYSQL_NUM);
                switch ($result[0][0]) {
                    case 1:
                        return new FormTemplate_Model($template_id);
                        break;
                    case 2:
                        return new InterviewTemplate_Model($template_id);
                        break;
                }
            }
            return null;
        } else if ($column == 'choices') {
            // Order choices by field 'order'
            $this->orderby(array('order'=>'ASC'));
        } else if ($column == 'trigger') {
            $column = 'parent';
        } else if ($column == 'trigger_id') {
            $column = 'parent_id';
        } else if ($column == 'sub_separator') {
            return $this->isSubSeparator();
        }
        return parent::__get($column);
    }

    public function __set($column, $value) {
        if ($column == 'sub_separator') {
            $this->type_id = QuestionType_Model::SUB_SEPARATOR;
            return;
        }
        parent::__set($column,$value);
    }
    
    protected function retrieveChoices(& $array) {
        $choices = array();
        $index = 0;
        foreach($array as $key=>$value) {
            $keyText = "choice_text_";
            $keyValue = "choice_value_";
            if (substr($key, 0, strlen($keyText)) === $keyText) {
                $id = substr($key, strlen($keyText));
                $choice['text'] = $value;
                unset($array[$key]);
                $valueIndex = $keyValue.$id;
                if (isset($array[$valueIndex])) {
                    $choice['value'] = $array[$valueIndex];
                    unset($array[$valueIndex]);
                } else {
                    $choice['value'] = '';
                }
                $choice['index']=$index;
                $choices[] = $choice;
                $index++;
            }
        }
        return $choices;
    }

    protected function checkBooleans(& $array) {
        if (!isset($array['required']))
            $array['required']=0;
    }

    public function uniqueVariableName($name) {
        $escapedName = addslashes($name);
        if (strlen(trim($escapedName))==0) {
            return true;
        }
        if ($this->loaded) {
            $other = $this->db->query("SELECT `variables`.`id` from `variables`,`$this->table_name` WHERE `$this->table_name`.`id` != '$this->id' AND `$this->table_name`.`variable_id` = `variables`.`id` and `variables`.`name` = '$escapedName' AND `$this->table_name`.`template_id` = '$this->template_id'");
        } else {
            $other = $this->db->query("SELECT `variables`.`id` from `variables`,`$this->table_name` WHERE `variables`.`name` = '$escapedName' AND `$this->table_name`.`variable_id` = `variables`.`id` AND `$this->table_name`.`template_id` = '$this->template_id'");
        }
        return !($other->count() > 0);
    }

    public function removeTriggers($save = true) {
        $query = "delete from `question_triggers` where `question_id` = '$this->id'";
        if($this->db->query($query)) {
            $this->parent_id = 0;
            if ($save)
                $this->save();
            return true;
        }
        return false;
    }

    public function addTriggers($triggerId, $choiceIds, $save = true) {
        if (($this->parent_id>0)&&($this->parent_id!=$triggerId)) {
            $this->removeTriggers($save);
        }
        $this->parent_id = $triggerId;
        if ($save)
            $this->save();
        foreach ($choiceIds as $choiceId) {
            $query = "insert into `question_triggers` (`question_id` ,`choice_id`) values ('$this->id','$choiceId')";
            if (!$this->db->query($query))
                return false;
        }
        return true;
    }

    public function hasTriggers() {
        return ($this->parent_id > 0);
    }

    public function getTriggers() {
        $query = "select choices.* from `choices`, `question_triggers` where `question_triggers`.`question_id`='$this->id' and `question_triggers`.`choice_id`=`choices`.`id`";
        if ($result = $this->db->query($query))
            return new ORM_Iterator(ORM::factory('choice'),$result);
        else
            return false;
    }

    public function getTriggersIds() {
        if ($triggers = $this->getTriggers()) {
            return $triggers->primary_key_array();
        }
        return false;
    }

    public function isEditable() {
        if ($this->loaded) {
            return $this->template->isEditable();
        }
        return true;
    }

    public function getItemActions(& $user) {
        $itemActions = array();
        $itemActions[] = array('function'=>"displayItem", 'text'=>"question.display");
        if ($this->isEditableBy($user)&&$this->isEditable()) {
            if (!$this->isSeparator()) {
                $itemActions[] = array('function'=>"editItem", 'text'=>"question.edit");
                if ($this->isAdvanced())
                    $itemActions[] = array('function'=>"editTriggers", 'text'=>"question.edit_triggers");
                $itemActions[] = array('function'=>"deleteItem", 'text'=>"question.delete");
            } else {
                $itemActions[] = array('function'=>"editItem", 'text'=>"question.edit_separator");
                if ($this->isAdvanced())
                    $itemActions[] = array('function'=>"editTriggers", 'text'=>"question.edit_triggers");
                $itemActions[] = array('function'=>"deleteSeparator", 'text'=>"question.delete_separator");
            }
        }
        return $itemActions;
    }

    public function delete() {
        if ($this->loaded) {
            // first: delete choices if any
            $choices = $this->choices;
            foreach($choices as $choice) {
                $choice->delete();
            }

            // second: delete triggers if any
            if ($this->hasTriggers()) {
                $this->removeTriggers(false);
            }

            // third: delete variable
            $variable = $this->variable;
            $variable->delete();
            
            // fourth: delete element itself
            parent::delete();
        }
    }

    public function copy(& $question, & $template) {
        $this->text = $question->text;
        $this->description = $question->description;
        if ($template->questionAdvanced()) {
            $this->required = $question->required;
        }
        $this->type_id = $question->type_id;
        $this->order = $question->order;
        $this->template_id = $template->id;
        $this->save();

        if ($template->questionAdvanced()) {
            // handle variable
            $variable = ORM::factory('variable');
            $variable->copy($question->variable, $this);
            $this->variable_id = $variable->id;
            // handle choices if any
            $choices = $question->choices;
            foreach ($choices as $choice) {
                $copiedChoice = ORM::factory('choice');
                $copiedChoice->copy($choice, $this);
            }
            $this->save();
        }
        return $this->id;
    }

    public function copyTriggers(& $question, & $copiedQuestions) {
        if ($question->hasTriggers()) {
            $trigger = $question->parent;
            $newTrigger = $copiedQuestions[$trigger->id];
            if ($trigger->loaded) {
                $choices = $trigger->orderby('order')->choices->as_array();
                $newChoices = $newTrigger->orderby('order')->choices->as_array();
                if (sizeof($choices)!=sizeof($newChoices))
                    return false;
                $choiceIds = array();
                for ($i=0; $i<sizeof($choices); $i++) {
                    $choiceIds[$choices[$i]->id] = $newChoices[$i]->id;
                }
                $triggersIds = $question->getTriggersIds();
                $newTriggersIds = array();
                foreach ($triggersIds as $triggerId) {
                    $newTriggersIds[] = $choiceIds[$triggerId];
                }
                $this->removeTriggers(false);
                $this->addTriggers($newTrigger->id, $newTriggersIds, true);
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    public function exportInDocument($answers = null) {
        if (strlen($this->description)>0) {
            $description = "<i>$this->description</i>";
        } else {
            $description = null;
        }
        if ($this->isAdvanced()) {
            $longText = false;
            switch($this->type_id) {
                    case QuestionType_Model::LONG_TEXT :
                        $longText  =true;
                    case QuestionType_Model::INTEGER :
                    case QuestionType_Model::REAL :
                    case QuestionType_Model::TEXT :
                        $answer = null;
                        if (isset($answers)&&$answers->valid()) {
                             $value = $answers->current()->value;
                             $answer = $value->value;
                        }
                        rtf::addTextQuestion($this->text, $description, $longText, $answer);
                        break;
                    case QuestionType_Model::CHOICE :
                    case QuestionType_Model::MULTIPLE_CHOICE :
                        $choices = array();
                        $checked =  array();
                        $checkedIds = array();
                        if (isset($answers)) {
                            foreach($answers as $answer) {
                                $checkedIds[] = $answer->choice_id;
                            }
                        }
                        $index = 0;
                        foreach($this->choices as $choice) {
                            $choices[] = $choice->text;
                            if (in_array($choice->id, $checkedIds)) {
                                $checked[] = $index;
                            }
                            $index++;
                        }
                        $multiple = false;
                        if ($this->type_id == QuestionType_Model::MULTIPLE_CHOICE) {
                            $multiple = true;
                            if (isset ($description))
                                $description = $description."\n".Kohana::lang('question.export_multiple_choice');
                            else
                                $description = Kohana::lang('question.export_multiple_choice');
                        }
                        rtf::addQuestionWithChoices($this->text, $description, $choices, $multiple, $checked);
                        break;
                    case QuestionType_Model::SEPARATOR :
                    case QuestionType_Model::SUB_SEPARATOR :
                        rtf::addSeparator($this->text, $description, $this->isSubSeparator());
                        break;
            }
        } else {
            $answer = null;
            if (isset($answers)&&$answers->valid()) {
                 $value = $answers->current()->value;
                 $answer = $value->value;
            }
            rtf::addTextQuestion($this->text, $description, true, $answer);
        }
    }

}
?>