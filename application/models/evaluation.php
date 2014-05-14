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

class Evaluation_Model extends Toucan_Model {

    protected $belongs_to = array('owner' => 'user','activity');
    protected $has_many = array('formSessions', 'interviewSessions', 'indicators', 'categories');
    protected $has_one = array('view' => 'group', 'edit' => 'group', 'state' =>'evaluationState');
    protected $accessParent = "activity";
    protected $accessHeirs = array('formSessions', 'interviewSessions', 'indicators');
    protected $startDateValue = "";
    protected $endDateValue = "";
    
    
    public function getCreationData($access, & $user, & $parameters = null) {
        $this->activity_id = $parameters['activity_id'];
        $activity = $this->activity;
        if (!$this->valuesSet) {
            // Intialise inherited access control
            $this->inherit = 1;
            $this->view_id = $activity->getDisplayGroupId();
            $this->edit_id = $activity->getEditGroupId();
        }
        return $this->getEditableData(access::OWNER, $user);
    }

    public function getEditableData($access, & $user) {
        $admin = ($access == access::ADMIN);
        $owner = ($access == access::OWNER);
        $editableData = array();
        $editableData[] = array ('type' => 'text','name' => 'name','label' => 'evaluation.name','required'=>'1', 'value' => $this->name);
        $editableData[] = array ('type' => 'long_text','name' => 'description','label' => 'evaluation.description','value' => $this->description);
        $editableData[] = array ('type' => 'separator');
        $states = EvaluationState_Model::getTranslatedStates();
        $editableData[] = array ('type' => 'select','name' => 'state_id','label' => 'evaluation.state','required'=>'1', 'values' => $states, 'value'=>$this->state_id);
        $editableData[] = array ('type' => 'date','name' => 'start_date','label' => 'evaluation.start_date','value'=>$this->startDateValue);
        $editableData[] = array ('type' => 'date','name' => 'end_date','label' => 'evaluation.end_date','value'=>$this->endDateValue);
        if ($owner|$admin) {
            $editableData[] = array ('type' => 'separator');
            $this->addEditableGroups($editableData, "evaluation");
        }
        if ($this->loaded) {
            $editableData[] = array ('type' => 'hidden','name' => 'activity_id','value'=>$this->activity_id);
        }
        return $editableData;
    }

    public function getDisplayableData($access, & $user = null) {
        $admin = ($access == access::ADMIN);
        $owner = ($access == access::OWNER);
        $displayableData = array();
        // NAME & DESCRIPTION
        $displayableData[] = array ('type' => 'text', 'label' => 'evaluation.name', 'value'=> $this->name);
        $displayableData[] = array ('type' => 'long_text', 'label' => 'evaluation.description', 'value'=> $this->description);
        $displayableData[] = array ('type' => 'separator');
        $displayableData[] = array ('type' => 'text', 'label' => 'evaluation.state', 'value'=> $this->state->getTranslatedName());
        $displayableData[] = array ('type' => 'text', 'label' => 'evaluation.start_date', 'value'=> $this->startDateValue);
        $displayableData[] = array ('type' => 'text', 'label' => 'evaluation.end_date', 'value'=> $this->endDateValue);

        if ($owner|$admin) {
            $displayableData[] = array ('type' => 'separator');
            $this->addDisplayableGroups($displayableData, "evaluation");
            $displayableData[] = array ('type' => 'separator');
            // OWNER
            $displayableData[] = array ('type' => 'link', 'label' => 'evaluation.owner', 'value'=> $this->owner->fullName, 'link'=> '/user/profile/'.$this->owner->id);
            // Creation date
            $displayableData[] = array ('type' => 'text', 'label' => 'evaluation.creation_date', 'value'=> Utils::translateTimestamp($this->created));
        }
        return $displayableData;
    }

    public function getDisplayableIndicators(& $user, $ids, $pos, $count, $publicAccess = false) {
        $displayableIndicators = array();
        
        $sliceIds = array_slice($ids, $pos, $count);
        $indicators = ORM::factory('indicator')->in('id', $sliceIds)->find_all();
        
        $indices = array_flip($sliceIds);
        
        foreach ($indicators as $indicator) {
            $item = $indicator->getDisplayableItemData($user, $publicAccess);
            if (isset($item)) {
                $displayableIndicators[$indices[$indicator->id]] = $item;
            }
        }
        ksort($displayableIndicators);
        return $displayableIndicators;
    }

    public function getIndicators(& $user, $categoryId = null) {
        $nullValue = null;
        $constraints = array('evaluation_id'=>$this->id);
        if (isset($categoryId)) {
            $category = ORM::factory('category', $categoryId);
            if (isset($category)&&!$category->isRecapitulative())
                $constraints['category_id'] = $categoryId;
        }
        return ORM::factory('indicator')->getItems($nullValue,$user,0, $nullValue, $constraints);
    }
    
    public function getIndicatorIds(& $user, $categoryId = null) {
        $indicators = $this->getIndicators($user, $categoryId);
        if (isset($indicators)&& count($indicators)>0)
            return $indicators->primary_key_array();
        else
            return array();
    }
    
    public function getPublicIndicatorIds(& $user, $categoryId = null) {
        $nullValue = null;
        $constraints = array('evaluation_id'=>$this->id);
        if (isset($categoryId)) {
            $category = ORM::factory('category', $categoryId);
            if (isset($category)&&!$category->isRecapitulative())
                $constraints['category_id'] = $categoryId;
        }
        $indicators = ORM::factory('indicator')->getPublicItems($nullValue,$user,0, $nullValue, $constraints);
        if (isset($indicators)&& count($indicators)>0)
            return $indicators->primary_key_array();
        else
            return array();
    }
    

    public function getCategories(& $user, $includeRecapitulative = true) {
        $nullValue = null;
        if (!$includeRecapitulative)
            $constraints = array('evaluation_id'=>$this->id,'active'=>1, 'recapitulative'=>0);
        else
            $constraints = array('evaluation_id'=>$this->id,'active'=>1);
        return ORM::factory('category')->getItems($nullValue,$user,0, $nullValue, $constraints);
    }

    
    public function getDisplayableCategories(& $user) {
        $displayableCategories = array();
        $categories = $this->getCategories($user);
        foreach ($categories as $category) {
            $item = array();
            $item['id'] = $category->id;
            $item['title'] = $category->name;
            $item['name'] = $category->name;
            $item['description'] = $category->description;
            $item['content'] = array();
            if ($category->isRecapitulative())
                $item['content'][] = array('type'=>'text', 'label'=>'category.is_recapitulative', 'value'=>'');
            $item['color'] = $category->getColor();
            $item['actions'] = $category->getItemActions($user);
            $displayableCategories[$category->id] = $item;
        }
        return $displayableCategories;
    }

    
    protected function buildValidation(array & $array) {
        $this->validation = Validation::factory($array)
            ->pre_filter('trim')
            ->add_rules('name', 'required', 'length[1,127]')
            ->add_callbacks('name', array($this, 'uniqueNameByActivity'))
            ->add_rules('description', 'length[0,500]')
            ->add_rules('view_id', 'valid::numeric')
            ->add_rules('edit_id', 'valid::numeric')
            ->add_rules('activity_id', 'required', 'valid::numeric')
            ->add_callbacks('start_date',array($this, 'validDate'))
            ->add_callbacks('end_date',array($this, 'validDate'))
            ->add_callbacks('end_date',array($this, 'endAfterStart'))
            ->add_rules('inherit', 'in_array[0,1]')
            ->add_rules('state_id', 'valid::numeric');
    }

    protected function checkBooleans(array & $array, & $user) {
        if ($user->isAdmin()||$this->isOwner($user)) {
            if (!isset($array['inherit']))
                $array['inherit']=0;
        }

    }

    public function validateEdition(array & $array,& $user, $save = FALSE) {
        // intialize booleans
        $this->checkBooleans($array, $user);
        $this->buildValidation($array);
        return parent::validate($this->validation, $save);
    }

    public function validateCreation(array & $array,& $user, $save = FALSE) {
        $this->setOwner($user, false);
        if ($result = $this->validateEdition($array, $user, false)) {
            $this->created = time();
            if ($save)
                $this->save();
        }
        return $result;
    }

    public function count(& $filter , & $user, $constraintId = null) {
        $constraints = null;
        if (isset($constraintId)) {
            if (is_array($constraintId))
                $constraints = $constraintId;
            else
                $constraints = array('activity_id' => $constraintId);
        }
        return $this->countVisibleItems($filter , $user, $constraints);
    }

    public function getItems(& $filter,& $user,$offset = 0, $number = null, $constraintId = null) {
        $constraints = null;
        if (isset($constraintId)) {
            if (is_array($constraintId))
                $constraints = $constraintId;
            else
                $constraints = array('activity_id' => $constraintId);
        }
        return $this->getVisibleItems($filter , $user, $offset, $number, $constraints);
    }

    public function uniqueNameByActivity(Validation $valid) {
        if (array_key_exists('name', $valid->errors()))
            return;
        if (isset ($valid->activity_id)) {
            $escapedName = addslashes($valid->name);
            $activityId = $valid->activity_id;
            if ($this->loaded) {
                $other = $this->db->query("SELECT id from ".$this->table_name." WHERE id != $this->id AND activity_id = '$activityId' AND name = '$escapedName'");
            } else {
                $other = $this->db->query("SELECT id from ".$this->table_name." WHERE activity_id = '$activityId' AND name = '$escapedName'");
            }
            if ($other->count() > 0) {
                $valid->add_error( 'name', 'uniqueName');
            }
        }
    }

    public function setValues(& $array) {
        parent::setValues($array);
        if (isset($array['start_date'])) {
            $this->startDateValue = $array['start_date'];
        }
        if (isset($array['end_date'])) {
            $this->endDateValue = $array['end_date'];
        }
    }

    public function __get($column) {
        if ($column == 'indicators') {
            // Order indicators by field 'order'
            $this->orderby(array('order'=>'ASC'));
        } else if ($column == 'formSessions') {
            $this->where('type',1);
        } else if ($column == 'interviewSessions') {
            $this->where('type',2);
        }
        return parent::__get($column);
    }
    
    public function load_values(array $values) {
        parent::load_values($values);
        $this->startDateValue = Utils::translateDate($this->start_date);
        $this->endDateValue = Utils::translateDate($this->end_date);
        return $this;
    }


    public function clearCache() {
        foreach ($this->indicators as $indicator) {
            $indicator->clearCache();
        }
    }

    public function isOver() {
        return (($this->loaded)&&($this->state_id==EvaluationState_Model::OVER));
    }

    public function isOpen() {
        return (($this->loaded)&&($this->state_id==EvaluationState_Model::GOING_ON));
    }

    public function delete() {
        if ($this->loaded) {
            // first: delete sessions
            $formSessions = $this->formSessions;
            foreach ($formSessions as $session) {
                $session->delete();
            }
            $interviewSessions = $this->interviewSessions;
            foreach ($interviewSessions as $session) {
                $session->delete();
            }

            // second: delete indicators
            $indicators = $this->indicators;
            foreach ($indicators as $indicator) {
                $indicator->delete();
            }

            // third: delete evaluation
            parent::delete();

        }
    }

    public function exportIndicators(& $user, $categoryId = null) {
        $logo = null;
        $category = null;
        
        if ($this->activity->logo_id>0)
            $logo = $this->activity->logo->path;
        rtf::initDocument(sprintf(Kohana::lang('evaluation.export_title'), $this->activity->name), sprintf(Kohana::lang('evaluation.export_subtitle'), $this->name), $logo);
        if (isset($categoryId)) {
            $category = ORM::factory('category', $categoryId);
            if (isset($category)) {
                $category->export();
            }
        }

        $indicators = $this->getIndicators($user, $categoryId);
        foreach ($indicators as $indicator) {
            $indicator->export();
        }
        if (isset($category))
            $title = sprintf(Kohana::lang('evaluation.export_file_name_with_category'), $this->name, $category->name);
        else
            $title = sprintf(Kohana::lang('evaluation.export_file_name'), $this->name);
        rtf::sendDocument($title);
    }

    public function hasForms() {
        return ($this->loaded && $this->formSessions->count()>0);
    }

    public function hasCategories(& $user) {
        return ($this->loaded && count($this->getCategories($user))>0);
    }
    
    public function copyIndicators(& $indicatorsIds,& $user, $sessionId = null, & $variables) {
        foreach($indicatorsIds as $indicatorId) {
            $indicator = ORM::factory('indicator', $indictorId);
            $parameters = null;
            if (isset($sessionId)) {
                $parameters = array('session_id'=>$sessionId);
            }
            $indicator->copyTo($this->id, $user, $parameters, $variables);
        }
    }
    
    public function getManualIndicators(& $user) {
        $nullValue = null;
        return ORM::factory('indicator')->getItems($nullValue,$user,0, $nullValue, array('evaluation_id'=>$this->id, 'type'=>Indicator_Model::TYPE_MANUAL));
    }

    public function getManualIndicatorsIds(& $user) {
        $manualIndicators = $this->getManualIndicators($user);
        if (count($manualIndicators)>0)
            return $manualIndicators->primary_key_array();
        return array();
    }

    public function indicatorsUpdated() {
        $indicatorsNotUpdated = $this->db->query("SELECT id from indicators WHERE evaluation_id = '$this->id' AND type != '".Indicator_Model::TYPE_MANUAL."' AND cached_value IS NULL AND cached_graphic_id IS NULL");
        return ($indicatorsNotUpdated->count() == 0);
    }
    
}
?>