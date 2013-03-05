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

class Survey_Model extends FormSession_Model {

    protected $accessParent = "activity";
    protected $table_name = "sessions";
    protected $parentId = "activity_id";
    protected $accessPrefix ="survey";
    protected $actual_object_name = "session";
    protected $indicatorModel = 'surveyIndicator';
    protected $has_many = array('formCopies','indicators', 'surveyCategories');

    
    public function delete() {
        if ($this->loaded) {
            // First: delete all corresponding copies
            $copies = $this->formCopies;
            foreach ($copies as $copy) {
                $copy->delete();
            }
            // Second: delete all indicators
            $indicators = ORM::factory('surveyIndicator')->where('evaluation_id','0')->where('session_id',$this->id)->find_all();
            foreach ($indicators as $indicator) {
                $indicator->delete();
            }
        }
        
        // Third: delete the element itself
        parent::delete();
    }
    
    public function clearCache() {
        $indicators = ORM::factory('surveyIndicator')->where('evaluation_id','0')->where('session_id',$this->id)->find_all();
        foreach ($indicators as $indicator) {
            $indicator->clearCache();
        }
    }
    
    public function isEditable() {
        return true;
    }

    public function isParentOpen() {
        return true;
    }

    protected function getPublicUrl(){
        if (($this->mayBeEditedByPublic())&&($this->loaded)) {
            return "publicSurvey/form/".$this->id;
        }
    }
    
    public function exportInDocument($includePrivate = false) {
        $logo = null;
        $activity = $this->activity;
        if ($activity->logo_id>0)
            $logo = $activity->logo->path;
        rtf::initDocument($activity->name, $this->name, $logo);
        // description
        if (strlen($this->description)>0) {
            rtf::addParagraph($this->description);
        }
        // questions
        $questions = $this->template->getQuestions($includePrivate);
        foreach ($questions as $question) {
            $question->exportInDocument();
        }
        // send document
        rtf::sendDocument(sprintf(Kohana::lang('survey.export_document_name'), $this->name));
    }

/*    public function getDisplayableIndicators(&$user, $categoryId = null) {
        $displayableIndicators = array();
        $indicators = $this->getIndicators($user, $categoryId);
        foreach ($indicators as $indicator) {
            $item = array();
            $item['title'] = $indicator->name;
            $item['order'] = $indicator->order;
            $item['id'] = $indicator->id;
            try {
                $item['content'] = $indicator->getValue(access::MAY_VIEW);
            } catch (Exception $e) {
                $item['content'] = array(array('type'=>'text', 'label'=>'indicator.error', 'value'=>Kohana::lang($e->getMessage())));
            }
            $item['actions'] = $indicator->getItemActions($user);
            $color = $indicator->getColor();
            if (isset($color)) {
                $item['color'] = $color->code;
            }
            $displayableIndicators[] = $item;
        }
        return $displayableIndicators;
    }

 */
    
    public function getDisplayableIndicators(& $user, $ids, $pos, $count, $publicAccess = false) {
        $displayableIndicators = array();
        
        $sliceIds = array_slice($ids, $pos, $count);
        $indicators = ORM::factory('surveyIndicator')->in('id', $sliceIds)->find_all();

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

    
    public function getCategories(& $user, $includeRecapitulative = true) {
        $nullValue = null;
        if (!$includeRecapitulative)
            $constraints = array('session_id'=>$this->id,'active'=>1, 'recapitulative'=>0);
        else
            $constraints = array('session_id'=>$this->id,'active'=>1);
        return ORM::factory('surveyCategory')->getItems($nullValue,$user,0, $nullValue, $constraints);        
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
    
    public function exportIndicators(& $user) {
        $logo = null;
        if ($this->activity->logo_id>0)
            $logo = $this->activity->logo->path;
        rtf::initDocument(sprintf(Kohana::lang('survey.export_indicators_title'), $this->activity->name), sprintf(Kohana::lang('survey.export_indicators_subtitle'), $this->name), $logo);
        $indicators = $this->getIndicators($user);
        foreach ($indicators as $indicator) {
            $indicator->export();
        }
        rtf::sendDocument(sprintf(Kohana::lang('survey.export_indicators_file_name'), $this->name));
    }

    public function getIndicators(& $user, $categoryId = null) {
        $nullValue = null;
        $constraints = array('evaluation_id'=>'0','session_id'=>$this->id);
        if (isset($categoryId)) {
            $category = ORM::factory('category', $categoryId);
            if (isset($category)&&!$category->isRecapitulative())
                $constraints['category_id'] = $categoryId;
        }
        return ORM::factory('surveyIndicator')->getItems($nullValue,$user,0, $nullValue, $constraints);
    }
    
     public function getIndicatorIds(& $user, $categoryId = null) {
        $indicators = $this->getIndicators($user, $categoryId);
        if (isset($indicators) && count($indicators)>0)
            return $indicators->primary_key_array();
        else
            return array();
    }
    
    public function getPublicIndicatorIds(& $user, $categoryId = null) {
        $nullValue = null;
        $constraints = array('evaluation_id'=>'0','session_id'=>$this->id);
        if (isset($categoryId)) {
            $category = ORM::factory('category', $categoryId);
            if (isset($category)&&!$category->isRecapitulative())
                $constraints['category_id'] = $categoryId;
        }
        $indicators = ORM::factory('surveyIndicator')->getPublicItems($nullValue,$user,0, $nullValue, $constraints);
        if (isset($indicators)&& count($indicators)>0)
            return $indicators->primary_key_array();
        else
            return array();
    }

    
    public function hasForms() {
        return true;
    }
    
    public function hasCategories(& $user) {
        return ($this->loaded && $this->getCategories($user)->count()>0);
    }

    
    public function copyIndicators(& $indicatorsIds,& $user, & $variables) {
        foreach($indicatorsIds as $indicatorId) {
            $indicator = ORM::factory($this->templateIndicatorModel, $indicatorId);
            $parameters = array('indicator_model'=>$this->indicatorModel);
            $indicator->copyTo($this->id, $user, $parameters, $variables);
        }
    }

    public function count(& $filter , & $user, $constraints = null) {
        if (!isset($constraints))
            $constraints = array('evaluation_id' => 0);
        else
            $constraints = array_merge($constraints, array('evaluation_id' => 0));
        return parent::count($filter, $user, $constraints);
    }

    public function getItems(& $filter,& $user,$offset = 0, $number = null, $constraints = array()) {
        if (!isset($constraints))
            $constraints = array('evaluation_id' => 0);
        else
            $constraints = array_merge($constraints, array('evaluation_id' => 0));
        return parent::getItems($filter, $user, $offset, $number, $constraints);
    }
    
    public function indicatorsUpdated() {
        $indicatorsNotUpdated = $this->db->query("SELECT id from indicators WHERE session_id = '$this->id' AND type != '".Indicator_Model::TYPE_MANUAL."' AND cached_value IS NULL AND cached_graphic_id IS NULL");
        return ($indicatorsNotUpdated->count() == 0);
    }

    
}

?>