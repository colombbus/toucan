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

abstract class Template_Model extends Toucan_Model {

    const FORM_TYPE = 1;
    const INTERVIEW_TYPE = 2;
    const SHARED = 1;

    protected $table_name = "templates";
    protected $belongs_to = array('owner'=>'user');
    protected $has_one = array('view' => 'group', 'edit' => 'group');
    protected $has_many = array('questions', 'variables');
    protected $indicatorModel;
    public    $templateType = 0;

    abstract public function questionAdvanced();

    protected static $variablesMapping = array();

    public function getCreationData($access, & $user, & $parameters = null) {
        return $this->getEditableData(access::OWNER, $user);
    }

    public function getEditableData($access, & $user) {
        $admin = ($access == access::ADMIN);
        $owner = ($access == access::OWNER);
        $editableData = array();
        $filter = null;
        $editableData[] = array ('type' => 'text','name' => 'name','label' => 'template.name','required'=>'1', 'value' => $this->name);
        $editableData[] = array ('type' => 'long_text','name' => 'description','label' => 'template.description','value' => $this->description);
        if ($owner|$admin) {
            $editableData[] = array ('type' => 'separator');
            $this->addEditableGroups($editableData, "template");
        }
        return $editableData;
   }

    public function getDisplayableData($access, & $user = null) {
        $admin = ($access == access::ADMIN);
        $owner = ($access == access::OWNER);
        $displayableData = array();
        // NAME & DESCRIPTION
        $displayableData[] = array ('type' => 'text', 'label' => 'template.name', 'value'=> $this->name);
        $displayableData[] = array ('type' => 'long_text', 'label' => 'template.description', 'value'=> $this->description);
        // GROUPS
        if ($owner|$admin) {
            $displayableData[] = array ('type' => 'separator');
            $this->addDisplayableGroups($displayableData, "template");
            $displayableData[] = array ('type' => 'separator');
            // OWNER
            $displayableData[] = array ('type' => 'link', 'label' => 'template.owner', 'value'=> $this->owner->fullName, 'link'=> '/user/profile/'.$this->owner->id);
            // Creation date
            $displayableData[] = array ('type' => 'text', 'label' => 'template.creation_date', 'value'=> Utils::translateTimestamp($this->created));
        }
        return $displayableData;
    }

    public function getDisplayableQuestions(& $user) {
        $displayableQuestions = array();
        $separatorColor = Kohana::config('toucan.separator_color');
        $subSeparatorColor = Kohana::config('toucan.sub_separator_color');
        $privateColor = Kohana::config('toucan.private_question_color');
        foreach($this->questions as $question) {
            $item = array();
            $item['title'] = $question->text;
            $item['order'] = $question->order;
            $item['id'] = $question->id;
            $item['content'] = $question->getDisplayableData(access::MAY_VIEW);
            $item['actions'] = $question->getItemActions($user);
            if ($question->isSeparator()) {
                if ($question->isSubSeparator())
                    $item['color'] = $subSeparatorColor;
                else
                    $item['color'] = $separatorColor;
            }
            if ($question->isPrivate()) {
                $item['title'] = sprintf(Kohana::lang('template.private_question'), $item['title']);
            }
            $displayableQuestions[] = $item;
        }
        return $displayableQuestions;
    }

    protected function buildValidation(array & $array) {
        $this->validation = Validation::factory($array)
            ->pre_filter('trim')
            ->add_rules('name', 'required', array($this, 'uniqueName'), 'length[1,127]')
            ->add_rules('description', 'length[0,10000]')
            ->add_rules('view_id', 'valid::numeric')
            ->add_rules('edit_id', 'valid::numeric');
    }

    public function validateEdition(array & $array,& $user, $save = FALSE) {
        $this->type = $this->templateType;
        $this->keepOldValues($array);
        $this->buildValidation($array);
        return parent::validate($this->validation, $save);
    }

    public function validateCreation(array & $array,& $user, $save = FALSE) {
        $result = $this->validateEdition($array, $user, false);
        if ($result) {
            $this->type = $this->templateType;
            $this->setOwner($user, false);
            $this->created = time();
            $this->shared=self::SHARED;
            if ($save)
                $this->save();
        }
        return $result;
    }

    public function count(& $filter , & $user, $constraintId = null) {
        return $this->countVisibleItems($filter , $user, array('type' => $this->templateType, 'shared'=>self::SHARED));
    }

    public function getItems(& $filter,& $user,$offset = 0, $number = null, $constraintId = null) {
        return $this->getVisibleItems($filter , $user, $offset, $number, array('type' => $this->templateType, 'shared'=>self::SHARED));
    }

    protected function keepOldValues(& $array) {
        if (!isset($array['view_id']))
            $array['view_id'] = $this->view_id;
        if (!isset($array['edit_id']))
            $array['edit_id'] = $this->edit_id;
    }

    public function getNextOrder() {
        $result = $this->db->query("SELECT max(`order`)+1 from questions WHERE template_id = $this->id");
        $result->result(false,MYSQL_NUM);
        if ($result[0][0] === null)
            return 1;
        else
            return $result[0][0];
    }

    public function __get($column) {
        if ($column == 'questions') {
            // Order question by field 'order'
            $this->orderby(array('order'=>'ASC'));
        } else if ($column == 'shortDescription') {
            // retrieve 100 first characters of the description
            if (strlen($this->description)>100)
                return substr($this->description,0,100)."...";
            else
                return $this->description;
        } else  if ($column == 'session') {
            return $this->getSession();
        } else  if ($column == 'indicators') {
            return ORM::factory($this->indicatorModel)->where('template_id', $this->id)->find_all();
        }
        return parent::__get($column);
    }

    public function isEditable() {
        // a template is editable if it is shared or if the corresponding session is not in state "going on"
        if ($this->shared == self::SHARED)
            return true;
        $session = $this->getSession();
        return (isset($session)&&($session->state_id != SessionState_Model::GOING_ON));
    }

    public function isDeletable() {
        // shared template is always deletable
        // unshared template is not directly deletable
        return ($this->shared == self::SHARED);
    }

    
    public function getVariablesList($numericalOnly = false) {
        $result = array();
        foreach($this->questions as $question) {
            if (!$question->isSeparator()) {
                $variable = $question->variable;
                if ((!$numericalOnly)||($variable->numerical)) {
                    $result[$variable->id] = $variable->name;
                }
           }
        }
        return $result;
    }
    
    public function getVariablesIds($numericalOnly = false, $simple = null) {
        $result = array();
        foreach($this->questions as $question) {
            if (!$question->isSeparator()) {
                $variable = $question->variable;
                if ((!$numericalOnly)||($variable->numerical)) {
                    if (isset($simple)) {
                        if ($simple) {
                            if ($variable->question->type_id != QuestionType_Model::MULTIPLE_CHOICE)
                                $result[] = $variable->id;
                        } else {
                            if ($variable->question->type_id == QuestionType_Model::MULTIPLE_CHOICE)
                                $result[] = $variable->id;
                        }
                    } else
                        $result[] = $variable->id;
                }
           }
        }
        return $result;
    }
    
    public function getVariablesInfo() {
        $result = array();
        foreach($this->questions as $question) {
            if (!$question->isSeparator()) {
                $variable = $question->variable;
                $result[$variable->id] = array('name' => $variable->name, 'numerical'=>$variable->numerical, 'simple'=> !$variable->isMultiple());
            }
        }
        return $result;
    }
    

    public function delete() {
        if ($this->loaded && $this->isEditable()) {
            // first: delete questions
            $questions = $this->questions;
            foreach ($questions as $question) {
                $question->delete();
            }
            // second: delete variables
            $variables = $this->variables;
            foreach ($variables as $variable) {
                $variable->delete();
            }
            // third: delete indicators
            $indicators = $this->indicators;
            foreach ($indicators as $indicator) {
                $indicator->delete();
            }
            // third: delete element itself
            parent::delete();
        }
    }

    public function copy(& $template) {
        // 1st copy questions
        $copiedQuestions = array();
        $variables = array();
        $questions = $template->questions;
        foreach ($questions as $question) {
            $copiedQuestion = ORM::factory('question');
            $copiedQuestion->copy($question, $this);
            $copiedQuestions[$question->id] = $copiedQuestion;
            if ($this->questionAdvanced()) {
                // deal with variables
                $variables[$question->variable_id] = $copiedQuestion->variable_id;
            }
        }

        // 2nd copy triggers
        $questions->rewind();
        foreach ($questions as $question) {
            $copiedQuestions[$question->id]->copyTriggers($question, $copiedQuestions);
        }
        
        // Required in order to reset database (to be investigated...)
        $template->push();
        
        return $variables;
    }

    public function exportInDocument($includePrivate = true) {
        rtf::initDocument($this->name);
        // description
        if (strlen($this->description)>0) {
            rtf::addParagraph($this->description);
        }
        // questions
        $questions = $this->getQuestions($includePrivate);
        foreach ($questions as $question) {
            $question->exportInDocument();
        }
        // send document
        rtf::sendDocument(sprintf(Kohana::lang('template.export_file_name'), $this->name));
    }
    
    public function newInstance(& $user) {
        $instance = ORM::factory($this->object_name);
        $instance->type = $this->templateType;
        $instance->setOwner($user, false);
        $instance->created = time();
        $instance->save();
        $variables = $instance->copy($this);
        self::$variablesMapping[$this->id] = $variables;
        return $instance;
    }
    
    public function getMapping() {
        if (isset(self::$variablesMapping[$this->id]))
            return self::$variablesMapping[$this->id];
        return null;
    }
  
    public function isViewableBy(& $user) {
        if ($this->shared==self::SHARED)
            return parent::isViewableBy($user);
        $session = $this->getSession();
        if (isset($session))
            return $session->isViewableBy($user);
        return false;
    }

    public function isEditableBy(& $user) {
        if ($this->shared==self::SHARED)
            return parent::isEditableBy($user);
        $session = $this->getSession();
        if (isset($session))
            return $session->isEditableBy($user);
        return false;
    }

    protected function getSession() {
        if ($this->templateType == self::FORM_TYPE)
            $model = ORM::factory('formSession');
        else
            $model = ORM::factory('interviewSession');
        $sessions = $model->where('template_id',$this->id)->find_all();
        if ($sessions->count()==1) {
            $session = $sessions->current();
            if ($session->evaluation_id == 0 && $session->activity_id >0)
                // it is a survey
                return ORM::factory('survey', $session->id);
            return $session;
        }
        return null;
    }
    
    public function uniqueName($name) {
        $escapedName = addslashes($name);
        if ($this->loaded) {
            $other = $this->db->query("SELECT id from ".$this->table_name." WHERE id != $this->id AND name = '$escapedName' AND shared='".self::SHARED."'");
        } else {
            $other = $this->db->query("SELECT id from ".$this->table_name." WHERE name = '$escapedName' AND shared='".self::SHARED."'");
        }
        return !($other->count() > 0);
    }
    
    public static function getTemplate($templateId) {
        $db = Database::instance();
        $result = $db->query("SELECT type from templates WHERE id = $templateId");
        $result->result(false,MYSQL_NUM);
        switch ($result[0][0]) {
            case Template_Model::FORM_TYPE:
                return new FormTemplate_Model($templateId);
                break;
            case Template_Model::INTERVIEW_TYPE:
                return new InterviewTemplate_Model($templateId);
                break;
        }
        return null;
    }
    
    public function getDisplayableIndicators(& $user) {
        $displayableIndicators = array();
        $nullValue = null;
        $indicators = ORM::factory($this->indicatorModel)->getItems($nullValue,$user,0, $nullValue, array('template_id'=>$this->id));
        foreach ($indicators as $indicator) {
            $item = array();
            $item['title'] = $indicator->name;
            $item['order'] = $indicator->order;
            $item['id'] = $indicator->id;
            $item['content'] = array();
            $item['actions'] = $indicator->getItemActions($user);
            $displayableIndicators[] = $item;
        }
        return $displayableIndicators;
    }
    
    public function copyIndicators(& $indicatorsIds,& $user, & $variables) {
        foreach($indicatorsIds as $indicatorId) {
            $indicator = ORM::factory($this->indicatorModel, $indicatorId);
            $parameters = null;
            $indicator->copyTo($this->id, $user, $parameters, $variables, true);
        }
    }
    
    public function getIndicators(& $user) {
        $nullValue = null;
        return ORM::factory('templateIndicator')->getItems($nullValue,$user,0, $nullValue, array('template_id'=>$this->id));
    }

    public function getIndicatorIds(& $user) {
        $indicators = $this->getIndicators($user);
        if (isset($indicators)&& count($indicators)>0)
            return $indicators->primary_key_array();
        else 
            return array();
    }

    public function getQuestions($includePrivate = false) {
        if (!$includePrivate)
            $this->where('private', 0);
        return $this->questions;
    }
    
    public function hasPrivateQuestions() {
        $this->where('private', 1);
        return (count($this->questions)>0);
    }

}
?>
