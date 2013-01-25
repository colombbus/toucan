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

class Indicator_Model extends Toucan_Model implements Ajax_Model {

    protected $belongs_to = array('evaluation','owner'=>'user','session'=>'formSession', 'template');
    protected $has_one = array('view' => 'group', 'edit' => 'group','contribute' => 'group', 'value'=>'indicatorValue', 'variable', 'calculation', 'graphic', 'evaluator'=>'user', 'cached_graphic'=>'file');
    protected $has_many = array('indicatorValues', 'individuals', 'limits');
    protected $accessParent = 'generic_parent';
    protected $computationAlreadyPerformed = false;
    protected $controllerName = "indicator";
    protected $parentName = "evaluation";
    protected $parentId = "evaluation_id";
    protected $sessionName = "session";
    protected $sessionId = "session_id";
    protected $displaySessionLink = true;
    protected $ignored_columns = array('generic_parent', 'indicator_model');

 
    const TYPE_MANUAL = 1;
    const TYPE_AUTOMATIC_NUMERICAL = 2;
    const TYPE_AUTOMATIC_GRAPHIC = 3;
    const OPERATOR_AND = 1;
    const OPERATOR_OR = 2;

    public function getCreationData($access, & $user, & $parameters = null) {
        if (isset($parameters['type']))
            $this->type = $parameters['type'];
        if (!isset($this->type)||($this->type==0))
            $this->type = self::TYPE_MANUAL;
        $parentId = $this->parentId;
        $parentName = $this->parentName;
        if (isset($parameters) && isset($parameters[$parentId])) {
            // Initialize inherited access control
            $this->$parentId = $parameters[$parentId];
            $this->inherit = 1;
            $parent = $this->$parentName;
            $this->view_id = $parent->getDisplayGroupId();
            $this->edit_id = $parent->getEditGroupId();
        }
        $admin = ($access == access::ADMIN);
        $owner = ($access == access::OWNER);
        $creationData = array();
        $creationData[] = array('type'=>'text', 'label'=>'indicator.name', 'name'=>'name','value'=>$this->name, 'required'=>'1');
        $creationData[] = array('type'=>'long_text', 'label'=>'indicator.description', 'name'=>'description','value'=>$this->description);
        if ($this->type == self::TYPE_AUTOMATIC_GRAPHIC || $this->type == self::TYPE_AUTOMATIC_NUMERICAL) {
            $sessionId = $this->sessionId;
            $sessions = $this->getSessions();
            if (sizeof($sessions) > 1) {
                $sessionNames = array();
                foreach($sessions as $session) {
                    $sessionNames[$session->id] = $session->name;
                }
                $creationData[] = array ('type' => 'separator');
                $creationData[] = array('type'=>'select', 'id'=>'', 'label'=>'indicator.session', 'name'=>$sessionId,'values'=>$sessionNames, 'value'=>$this->$sessionId, 'required'=>'1');
            } else if (sizeof($sessions) == 1) {
                $session = $sessions[0];
                $creationData[] = array('type'=>'hidden', 'id'=>'', 'name'=>$sessionId, 'value'=>$session->id);
            }
        } 
        if ($owner|$admin) {
            $creationData[] = array ('type' => 'separator');
            $this->addEditableGroups($creationData, $this->controllerName);
            if ($this->type != self::TYPE_MANUAL) {
                $creationData[sizeof($creationData)-1]['hidden'] = 1;
            }
        }
        if ($this->loaded) {
            $creationData[] = array ('type' => 'hidden','name' => 'order', 'value' => $this->order);
            $creationData[] = array ('type' => 'hidden','name' => $parentId, 'value' => $this->$parentId);
            $creationData[] = array ('type' => 'hidden','name' => 'type', 'value' => $this->type);
        }
        return $creationData;
    }

    public function getEditableData($access, & $user) {
        $admin = ($access == access::ADMIN);
        $owner = ($access == access::OWNER);
        $editableData = array();
        $editableData[] = array('type'=>'text', 'label'=>'indicator.name', 'name'=>'name','value'=>$this->name, 'required'=>'1');
        $editableData[] = array('type'=>'long_text', 'label'=>'indicator.description', 'name'=>'description','value'=>$this->description);
        $sessionId = $this->sessionId;
        $sessions = $this->getSessions();
        $types = array(self::TYPE_MANUAL=>Kohana::lang($this->controllerName.'.manual'),self::TYPE_AUTOMATIC_NUMERICAL=>Kohana::lang($this->controllerName.'.automatic_numerical'), self::TYPE_AUTOMATIC_GRAPHIC=>Kohana::lang($this->controllerName.'.automatic_graphic'));
        if (sizeof($sessions) > 1) {
            $sessionNames = array();
            foreach($sessions as $session) {
                $sessionNames[$session->id] = $session->name;
            }
            $editableData[] = array ('type' => 'separator');
            $editableData[] = array('type'=>'select', 'label'=>'indicator.type', 'name'=>'type','values'=>$types, 'value'=>$this->type, 'required'=>'1');
            $editableData[] = array('type'=>'select', 'id'=>'', 'label'=>'indicator.session', 'name'=>$sessionId,'values'=>$sessionNames, 'value'=>$this->$sessionId, 'required'=>'1', 'hidden'=>($this->type==self::TYPE_MANUAL));

        } else if (sizeof($sessions) == 1) {
            $session = $sessions[0];
            $editableData[] = array ('type' => 'separator');
            $editableData[] = array('type'=>'select', 'label'=>'indicator.type', 'name'=>'type','values'=>$types, 'value'=>$this->type, 'required'=>'1');
            $editableData[] = array('type'=>'hidden', 'id'=>'', 'name'=>$sessionId, 'value'=>$session->id);
        } else {
            $types = array(self::TYPE_MANUAL=>Kohana::lang('indicator.manual'));
            $editableData[] = array('type'=>'select', 'label'=>'indicator.type', 'name'=>'type','values'=>$types, 'value'=>$this->type, 'required'=>'1');
            $editableData[] = array('type'=>'hidden', 'id'=>'', 'name'=>$sessionId, 'value'=>$this->$sessionId);
        }
        if (($owner|$admin)&&(!$this->belongsToTemplate())) {
            $editableData[] = array ('type' => 'separator');
            $this->addEditableGroups($editableData, $this->controllerName);
            if ($this->type != self::TYPE_MANUAL) {
                $editableData[sizeof($editableData)-1]['hidden'] = 1;
            }
        }
        if ($this->loaded) {
            $parentId = $this->parentId;
            $editableData[] = array ('type' => 'hidden','name' => 'order', 'value' => $this->order);
            $editableData[] = array ('type' => 'hidden','name' => $parentId, 'value' => $this->$parentId);
        }
        return $editableData;
    }

    public function getDisplayableData($access, & $user = null) {
        $admin = ($access == access::ADMIN);
        $owner = ($access == access::OWNER);
        $displayableData = array();
        $displayableData[] = array('type'=>'text', 'label'=>'indicator.name', 'value'=>$this->name);
        $displayableData[] = array('type'=>'long_text', 'label'=>'indicator.description', 'value'=>$this->description);
        $displayableData[] = array ('type' => 'separator');
        switch ($this->type) {
            case self::TYPE_MANUAL :
                $typeText = Kohana::lang($this->controllerName.'.manual');
                break;
            case self::TYPE_AUTOMATIC_NUMERICAL :
                $typeText = Kohana::lang($this->controllerName.'.automatic_numerical');
                break;
            case self::TYPE_AUTOMATIC_GRAPHIC :
                $typeText = Kohana::lang($this->controllerName.'.automatic_graphic');
                break;
        }
        $displayableData[] = array('type'=>'text', 'label'=>'indicator.type', 'value'=>$typeText);
        if ($this->type == self::TYPE_MANUAL) {
            $displayableData[] = array('type'=>'link', 'label'=>'indicator.values', 'value'=>Kohana::lang('indicator.see_values'), 'link'=>$this->controllerName."/values/$this->id" );
        } else if ($this->displaySessionLink) {
            $displayableData[] = array('type'=>'link', 'label'=>'indicator.session', 'value'=>$this->session->name, 'link'=>'formSession/show/'.$this->session_id);
        }
        if (($owner|$admin)&&!$this->belongsToTemplate()) {
            $displayableData[] = array('type'=>'separator');
            $this->addDisplayableGroups($displayableData, $this->controllerName);
            if ($this->type != self::TYPE_MANUAL) {
                // remove information about contributors
                unset($displayableData[sizeof($displayableData)-1]);
            }
            if (($this->type == self::TYPE_MANUAL)&&($this->evaluator_id>0)) {
                $displayableData[] = array('type'=>'separator');
                $displayableData[] = array('type'=>'link', 'label'=>'indicator.evaluator', 'value'=>$this->evaluator->fullName, 'link'=>'user/profile/'.$this->evaluator_id);
                $displayableData[] = array('type'=>'text', 'label'=>'indicator.set_date', 'value'=>Utils::translateTimestamp($this->set_date));
                $displayableData[] = array('type'=>'long_text', 'label'=>'indicator.explanations', 'value'=>$this->explanations);
            }
        }

        if ($admin||$owner) {
            $displayableData[] = array ('type' => 'separator');
            // OWNER
            $displayableData[] = array ('type' => 'link', 'label' => 'indicator.owner', 'value'=> $this->owner->fullName, 'link'=> '/user/profile/'.$this->owner->id);
            // Creation date
            $displayableData[] = array ('type' => 'text', 'label' => 'indicator.creation_date', 'value'=> Utils::translateTimestamp($this->created));
        }

        return $displayableData;
    }

    protected function computeValue() {
        if ($this->computationAlreadyPerformed) {
            if (isset($this->cached_value))
                return $this->cached_value;
            else
                return null;
        }
        $this->computationAlreadyPerformed = true;
        $populationIds = $this->getPopulationIds();
        if (sizeof($populationIds)==0)
            return null;
        if ($this->calculation->requires_variable) {
            $population = ORM::factory('formCopy')->in('id',$populationIds)->find_all();
            $values = array();
            $variable = $this->variable;
            foreach($population as $copy) {
                $value = $variable->getValue($copy);
                if (is_array($value)) {
                    $values = array_merge($values, $value);
                } else if (isset($value))
                    $values[] = $value;
            }
        } else {
            $values = $populationIds;
        }
        if (method_exists('calculation', $this->calculation->name)) {
            $methodName = $this->calculation->name;
            $value = calculation::$methodName($values);
            $this->cached_value = $value;
            $this->save();
            return $value;
        } else {
            return null;
        }
    }

    protected function computeGraphic() {
        $populationIds = $this->getPopulationIds();
        if (count($populationIds)==0)
			return null;
		$population = ORM::factory('formCopy')->in('id',$populationIds)->find_all();
        $values = array();
        $variable = $this->variable;
        $question = $variable->question;
        $valuesNumber = array();
        $labels = array();
        $not_answered = 0;

        if ($question->type_id == QuestionType_Model::CHOICE || $question->type_id == QuestionType_Model::MULTIPLE_CHOICE) {
            // initialize data array
            $choices = $question->choices;
            foreach($choices as $choice) {
                $values[$choice->id] = array('count'=>0, 'label'=>$choice->getValue($this->text_values));
            }
            foreach($population as $copy) {
                $id = $variable->getChoiceIds($copy);
                if (isset($id)) {
                    if (is_array($id)) {
                        foreach ($id as $single) {
                            $values[$single]['count']++;
                        }
                    } else {
                        $values[$id]['count']++;
                    }
                } else 
                    $not_answered++;
            }
            foreach ($values as $value) {
                $valuesNumber[] = $value['count'];
                $labels[] = $value['label'];
            }
        } else {
            foreach($population as $copy) {
                $value = $variable->getValue($copy, $this->text_values);
                if (is_array($value)) {
                    $values = array_merge($values, $value);
                } else {
                    $values[] = $value;
                }
            }

            $index = 0;
            $indeces = array();

            foreach ($values as $value) {
                if (isset($value)) {
                    if (isset($indeces[$value]))
                        $valuesNumber[$indeces[$value]]++;
                    else {
                        $indeces[$value] = $index;
                        $valuesNumber[$index] = 1;
                        $labels[$index] = $value;
                        $index++;
                    }
                } else {
                    $not_answered++;
                }
            }
        }

        // if it exists, put "not_answered" at the end
        if ($this->not_answered) {
            if ($not_answered>0) {
                $valuesNumber[] = $not_answered;
                $labels[] = Kohana::lang('indicator.not_answered');
            }
        }

        if ((count($valuesNumber)>0)&&method_exists('graphic', $this->graphic->name)) {
            $methodName = $this->graphic->name;
            if (!isset($this->cached_graphic_id)) {
                $cached_graphic = File_Model::newFile('graphics', '.png');
                $this->cached_graphic_id = $cached_graphic->id;
                $this->save();
            } else {
                $cached_graphic = $this->cached_graphic;
            }

            $info = array();
            $info['title'] = $this->graphic_title;
            $info['x_axis'] = $this->graphic_x_axis;
            $info['y_axis'] = $this->graphic_y_axis;

            graphic::$methodName($valuesNumber, $labels, $info, $cached_graphic->getFile());
            
            chmod($cached_graphic->getFile(), Kohana::config('toucan.public_file_mode'));

            return $cached_graphic;
        } else {
            return null;
        }
    }

    public function getValue() {
        $displayableData = array();
        switch ($this->type) {
            case self::TYPE_MANUAL:
                if ($this->value_id>0)
                    $displayableData[] = array('type'=>'text', 'label'=>'indicator.value', 'value'=>$this->value->name);
                else
                    $displayableData[] = array('type'=>'text', 'label'=>'indicator.value', 'value'=>Kohana::lang('indicator.not_set'));
                break;
            case self::TYPE_AUTOMATIC_NUMERICAL:
                if (isset($this->cached_value))
                    $value = $this->cached_value;
                else
                    $value = $this->computeValue();
                if (isset($value)) {
                    $displayableData[] = array('type'=>'text', 'label'=>'indicator.value', 'value'=>$value);

                } else {
                    $displayableData[] = array('type'=>'text', 'label'=>'indicator.value', 'value'=>Kohana::lang('indicator.calculation_not_set'));
                }
                break;
            case self::TYPE_AUTOMATIC_GRAPHIC:
                if (isset($this->cached_graphic_id)) {
                    $graphic = $this->cached_graphic;
                } else {
                    $graphic = $this->computeGraphic();
                }
                if (isset($graphic)) {
                    $displayableData[] = array('type'=>'images', 'path'=>$graphic->path);
                } else {
                    $displayableData[] = array('type'=>'text', 'label'=>'indicator.value', 'value'=>Kohana::lang('indicator.graphic_not_set'));
                }

        }
        $color = $this->getColor();
        if (isset($color)) {
            $displayableData[] = array('type'=>'color', 'code'=>$color->code);
        }
        return $displayableData;
    }

    public function getDisplayableValues(& $user) {
        $displayableValues = array();
        foreach ($this->indicatorValues as $value) {
            $item = array();
            $item['title'] = $value->name;
            $item['order'] = $value->order;
            $item['id'] = $value->id;
            $item['content'] = $value->getDisplayableData(access::MAY_VIEW);
            $item['actions'] = $value->getItemActions($user);
            if ($value->color_id>0)
                $item['color'] = $value->color->code;
            $displayableValues[] = $item;
        }
        return $displayableValues;
    }

    public function getDisplayableIndividuals(& $user) {
        $displayableIndividuals = array();
        $order = 1;
        foreach ($this->individuals as $individual) {
            $item = array();
            $item['title'] = $individual->name;
            $item['order'] = $order;
            $item['id'] = $individual->id;
            $item['content'] = $individual->getDisplayableData(access::MAY_VIEW);
            $item['actions'] = $individual->getItemActions($user);
            $displayableIndividuals[] = $item;
            $order++;
        }
        return $displayableIndividuals;
    }

    public function getDisplayableOperator() {
        return Kohana::lang('indicator.operator_'.$this->population_operator);
    }

    public function getDisplayableCalculation() {
        $displayableCalculation = array();
        $displayableCalculation[] = array('type'=>'text', 'label'=>'indicator.calculation_type', 'value'=>$this->calculation->translatedName);
        if ($this->calculation->requires_variable) {
            $displayableCalculation[] = array('type'=>'text', 'label'=>'indicator.calculation_variable', 'value'=>$this->variable->name);
        }
        return array_merge($displayableCalculation,$this->getValue());
    }

    public function getDisplayableLimits(& $user) {
        $displayableLimits = array();
        foreach ($this->limits as $limit) {
            $item = array();
            $item['title'] = $limit->name;
            $item['order'] = 0;
            $item['id'] = $limit->id;
            $item['content'] = $limit->getDisplayableData(access::MAY_VIEW);
            $item['actions'] = $limit->getItemActions($user);
            if ($limit->color_id>0)
                $item['color'] = $limit->color->code;
            $displayableLimits[] = $item;
        }
        return $displayableLimits;
    }

    public function getEditableCalculation() {
        $editableCalculation = array();
        $editableCalculation[] = array('type'=>'select', 'label'=>'indicator.calculation_type', 'name'=>'calculation_id','values'=>Calculation_Model::getTranslatedList(), 'value'=>$this->calculation_id, 'required'=>'1', 'id'=>'calculation_id');
        $editableCalculation[] = array('type'=>'select', 'label'=>'indicator.calculation_variable', 'name'=>'variable_id', 'values'=>$this->getVariablesList(true), 'value'=>$this->variable_id, 'required'=>1, 'hidden'=>!$this->calculation->requires_variable,'id'=>'variable_id');
        return $editableCalculation;
    }

    public function getDisplayableGraphic($includeGraphic = true) {
        $displayableGraphic = array();
        $displayableGraphic[] = array('type'=>'text', 'label'=>'indicator.graphic_type', 'value'=>$this->graphic->translatedName);
        $displayableGraphic[] = array('type'=>'text', 'label'=>'indicator.graphic_variable', 'value'=>$this->variable->name);
        if ($includeGraphic)
            return array_merge($displayableGraphic,$this->getValue());
        else
            return $displayableGraphic;
    }

    public function getEditableGraphic() {
        $editableGraphic = array();
        $editableGraphic[] = array('type'=>'select', 'label'=>'indicator.graphic_type', 'name'=>'graphic_id','id'=>'graphic_id','values'=>Graphic_Model::getTranslatedList(), 'value'=>$this->graphic_id, 'required'=>'1');
        $editableGraphic[] = array('type'=>'select', 'label'=>'indicator.graphic_variable', 'name'=>'variable_id', 'values'=>$this->getVariablesList(false), 'value'=>$this->variable_id, 'required'=>1);
        $editableGraphic[] = array('type'=>'check', 'label'=>'indicator.include_not_answered', 'name'=>'not_answered', 'checked'=>$this->not_answered);
        $editableGraphic[] = array('type'=>'check', 'label'=>'indicator.force_text_values', 'name'=>'text_values', 'checked'=>$this->text_values);
        $editableGraphic[] = array('type'=>'text', 'label'=>'indicator.graphic_title', 'name'=>'graphic_title', 'value'=>$this->graphic_title);
        $editableGraphic[] = array('type'=>'text', 'label'=>'indicator.x_axis', 'name'=>'graphic_x_axis', 'value'=>$this->graphic_x_axis, 'hidden'=>($this->graphic_id==Graphic_Model::PIE_CHART));
        $editableGraphic[] = array('type'=>'text', 'label'=>'indicator.y_axis', 'name'=>'graphic_y_axis', 'value'=>$this->graphic_y_axis, 'hidden'=>($this->graphic_id==Graphic_Model::PIE_CHART));
        return $editableGraphic;
    }

    protected function buildValidation(& $array) {
        $this->validation = Validation::factory($array)
            ->pre_filter('trim')
            ->add_rules('name', 'required', 'length[1,127]')
            ->add_callbacks('name', array($this, 'uniqueNameByParent'))
            ->add_rules('description', 'length[0,500]')
            ->add_rules('session_id', 'valid::numeric')
            ->add_rules('template_id', 'valid::numeric')
            ->add_rules('view_id', 'valid::numeric')
            ->add_rules('edit_id', 'valid::numeric')
            ->add_rules('contribute_id', 'valid::numeric')
            ->add_rules('evaluation_id', 'valid::numeric')
            ->add_rules('inherit', 'in_array[0,1]')
            ->add_rules('type', 'required', "in_array[".self::TYPE_MANUAL.",".self::TYPE_AUTOMATIC_NUMERICAL.",".self::TYPE_AUTOMATIC_GRAPHIC."]");
    }

    protected function checkBooleans(& $array,& $user) {
        if ($user->isAdmin()||$this->isOwner($user)) {
            if (!isset($array['inherit']))
                $array['inherit']=0;
        }
    }


    public function validateEdition(array & $array,& $user, $save = FALSE) {
        $this->checkBooleans($array, $user);
        $this->buildValidation($array);
        $result = parent::validate($this->validation, false);
        if ($result && $save) {
            $this->clearCache(false);
            $this->save();
        }
        return $result;
    }

    public function validateCreation(array & $array,& $user, $save = FALSE) {
        $this->setOwner($user, false);
        if ($result = $this->validateEdition($array, $user, false)) {
            $this->created = time();
            $parentId = $this->parentId;
            $this->order = $this->getNextOrder($this->$parentId);
            if ($this->belongsToTemplate()) {
                // force inherit if indicator belongs to template
                $this->inherit = 1;
            }
            if ($save) {
                $this->save();
            }
        }
        return $result;
    }

    public function validateCalculationEdition(array & $array,& $user, $save = FALSE) {
        $this->validation = Validation::factory($array)
            ->add_rules('calculation_id', 'valid::numeric','required')
            ->add_rules('variable_id', 'valid::numeric')
            ->add_callbacks('variable_id', array($this, 'checkVariable'));
        $result = parent::validate($this->validation, $save);
        if ($result) {
            $this->clearCache(false);
            if ($save)
                $this->save();
        }
        return $result;
    }

    public function validateGraphicEdition(array & $array,& $user, $save = FALSE) {
        if (!isset($array['not_answered'])) {
            $array['not_answered'] = 0;
        }
        if (!isset($array['text_values'])) {
            $array['text_values'] = 0;
        }
        $this->validation = Validation::factory($array)
            ->add_rules('graphic_id', 'valid::numeric','required')
            ->add_rules('graphic_title', 'length[0,127]')
            ->add_rules('graphic_x_axis', 'length[0,127]')
            ->add_rules('graphic_y_axis', 'length[0,127]')
            ->add_rules('not_answered', 'in_array[0,1]')
            ->add_rules('text_values', 'in_array[0,1]')
            ->add_rules('variable_id', 'valid::numeric');
            $result = parent::validate($this->validation, $save);
        if ($result) {
            $this->clearCache(false);
            if ($save)
                $this->save();
        }
        return $result;
    }


    public function count(& $filter , & $user, $constraintId = null) {
        // not implemented
    }

    public function getItems(& $filter,& $user,$offset = 0, $number = null, $constraints = null) {
        if (!isset ($filter)) {
            $filter = Filter::instance();
            $filter->setSorting("order");
        }
        $parentId = $this->parentId;
        if (isset($constraints[$parentId])) {
            // set parent_id to compute inherited access
            $this->$parentId = $constraints[$parentId];
        }
        return $this->getVisibleItems($filter , $user, $offset, $number, $constraints);
    }

    public function getNextOrder($parentId) {
        $result = $this->db->query("SELECT max(`order`)+1 from indicators WHERE ".$this->parentId." = $parentId");
        $result->result(false,MYSQL_NUM);
        if ($result[0][0] === null)
            return 1;
        else
            return $result[0][0];
    }

    public function uniqueNameByParent(Validation $valid) {
        if (array_key_exists('name', $valid->errors()))
            return;
        $parentId = $this->parentId;
        if (isset ($valid->$parentId)) {
            $escapedName = addslashes($valid->name);
            $idValue = $valid->$parentId;
            if ($this->loaded) {
                $other = $this->db->query("SELECT id from ".$this->table_name." WHERE id != $this->id AND $parentId = '$idValue' AND name = '$escapedName'");
            } else {
                $other = $this->db->query("SELECT id from ".$this->table_name." WHERE $parentId = '$idValue' AND name = '$escapedName'");
            }
            if ($other->count() > 0) {
                $valid->add_error( 'name', 'uniqueName');
            }
        }
    }

    public function __get($column) {
        if ($column == 'template') {
            if (isset ($this->template_id) && $this->template_id >0 ) {
                return Template_Model::getTemplate($this->template_id);
            }
            else if (isset($this->session_id)&& $this->session_id >0)
                return $this->session->template;
        }
        if ($column == 'generic_parent') {
            if ($this->evaluation_id >0) {
                // indicator linked to an evaluation
                return $this->evaluation;
            } else if ($this->template_id >0) {
                // indicator linked to a template
                return Template_Model::getTemplate($this->template_id);
            } else if ($this->session_id >0) {
                // indicator linked to a survey
                return ORM::factory("survey", $this->session_id);
            }
        }
        if ($column == 'indicatorValues') {
            // Order indicators by field 'order'
            $this->orderby(array('order'=>'ASC'));
        }
        return parent::__get($column);
    }

    public function changeOperator() {
        switch ($this->population_operator) {
            case self::OPERATOR_AND :
                $this->population_operator = self::OPERATOR_OR;
                break;
            case self::OPERATOR_OR :
                $this->population_operator = self::OPERATOR_AND;
                break;
        }
        $this->save();
    }

    public function checkVariable(Validation $valid) {
        if (array_key_exists('calculation_id', $valid->errors()))
            return;
        if (array_key_exists('variable_id', $valid->errors()))
            return;
        $calculation = ORM::factory('calculation', $valid->calculation_id);
        if (!isset($calculation)) {
            $valid->add_error( 'calculation_id', 'default');
            return;
        }
        if ($calculation->requires_variable) {
            if (!isset($valid->variable_id)||(strlen(trim($valid->variable_id))==0)) {
                $valid->add_error( 'variable_id', 'required');
            }
        }
    }

    public function getItemActions(& $user) {
        $itemActions = array();
        if (($this->type == self::TYPE_MANUAL) && $this->mayBeContributedBy($user)) {
            $itemActions[] = array('link'=>$this->controllerName."/set/", 'text'=>"indicator.set");
        }
        if ($this->type == self::TYPE_AUTOMATIC_GRAPHIC) {
            $itemActions[] = array('function'=>"displayItem", 'text'=>"indicator.display_graphic");
        }
        $itemActions[] = array('link'=>$this->controllerName."/show/", 'text'=>"indicator.details");
        if ($this->isEditableBy($user)) {
            $itemActions[] = array('function'=>"deleteItem", 'text'=>"indicator.delete");
        }
        return $itemActions;
    }

    protected function getPopulationIds() {
        $criteria = $this->individuals->as_array();
        $session = $this->session;
        $copies = $session->in('copies.state_id',CopyState_Model::getPublishedStates())->formCopies;
        $result = array();
        if (count($criteria)>0) {
            switch ($this->population_operator) {
                case self::OPERATOR_AND:
                    foreach ($copies as $copy) {
                        $in = true;
                        foreach($criteria as $criterion) {
                            if (!$criterion->isIn($copy)) {
                                $in = false;
                                break;
                            }
                        }
                        if ($in) {
                            $result[] = $copy->id;
                        }
                    }
                    break;
                case self::OPERATOR_OR:
                    foreach ($copies as $copy) {
                        $in = false;
                        foreach($criteria as $criterion) {
                            if ($criterion->isIn($copy)) {
                                $in = true;
                                break;
                            }
                        }
                        if ($in) {
                            $result[] = $copy->id;
                        }
                    }
                    break;
            }
        } else {
            foreach ($copies as $copy) {
                $result[] = $copy->id;
            }
        }
        return $result;
    }

    public function getSetData() {
        $editableData = array();
        $values = array();
        foreach ($this->indicatorValues as $value) {
            $values[$value->id] = $value->name;
        }
        $editableData[] = array('type'=>'select', 'label'=>'indicator.value', 'name'=>'value_id','value'=>$this->value_id, 'required'=>'1', 'values' => $values);
        $editableData[] = array('type'=>'long_text', 'label'=>'indicator.explanations', 'name'=>'explanations','value'=>$this->explanations);
        return $editableData;
    }


    public function validateSet(array & $array,& $user, $save = FALSE) {
        $this->validation = Validation::factory($array)
            ->pre_filter('trim')
            ->add_rules('value_id', 'required', 'valid::numeric')
            ->add_rules('explanations', 'length[0,500]');
        $result = parent::validate($this->validation, $save);
        if ($result && $save) {
            $this->evaluator_id = $user->id;
            $this->set_date = time();
            $this->save();
            return true;
        }
        return $result;
    }

    public function getColor() {
        switch ($this->type) {
            case self::TYPE_MANUAL :
                $value = $this->value;
                if ($value->color_id>0)
                    return $value->color;
                else
                    return null;
                break;
            case self::TYPE_AUTOMATIC_NUMERICAL :
                if (isset($this->cached_value))
                    $value = $this->cached_value;
                else
                    $value = $this->computeValue();
                if (isset($value)) {
                    foreach ($this->limits as $limit) {
                        if ($limit->contains($value)) {
                            return $limit->color;
                        }
                    }
                }
                return null;
                break;
            case self::TYPE_AUTOMATIC_GRAPHIC :
                return null;
                break;
        }
    }

    public function clearCache($save = true) {
        $this->cached_value = null;
        if (isset($this->cached_graphic_id)) {
            $graphic = $this->cached_graphic;
            $graphic->delete();
            $this->cached_graphic_id = null;
        }
        if ($save)
            $this->save();
    }

    public function delete() {
        if ($this->loaded) {
            // first: delete values if any
            $values = $this->indicatorValues;
            foreach ($values as $value) {
                $value->delete();
            }

            // second: delete limits if any
            $limits = $this->limits;
            foreach ($limits as $limit) {
                $limit->delete();
            }

            // third: delete individuals if any
            $individuals = $this->individuals;
            foreach ($individuals as $individual) {
                $individual->delete();
            }

            // fourth: delete cached graphic if any
            $this->clearCache(false);

            // fifth: delete element itself
            parent::delete();
        }
    }

    public function export() {
        switch ($this->type) {
            case self::TYPE_MANUAL :
                if ($this->value_id>0) {
                    if (strlen($this->value->description)>0)
                        $value = sprintf(Kohana::lang('indicator.export_value_with_description'), $this->value->name, $this->value->description);
                    else
                        $value = sprintf(Kohana::lang('indicator.export_value'), $this->value->name);
                    rtf::addParagraph($value, $this->name);
                    if (strlen($this->explanations)>0) {
                        rtf::addText("\n<i>".$this->explanations."</i>");
                    }
                } else
                    rtf::addParagraph(Kohana::lang('indicator.export_value_undefined'), $this->name);
                break;
            case self::TYPE_AUTOMATIC_NUMERICAL :
                if (isset($this->cached_value))
                    $value = $this->cached_value;
                else
                    $value = $this->computeValue();
                if (!isset($value)) {
                    $value = Kohana::lang('indicator.export_value_undefined');
                } else {
                    $value = sprintf(Kohana::lang('indicator.export_value'), $value);
                }
                rtf::addParagraph($value, $this->name);
                break;
            case self::TYPE_AUTOMATIC_GRAPHIC :
                if (isset($this->cached_graphic_id)) {
                    $graphic = $this->cached_graphic;
                } else {
                    $graphic = $this->computeGraphic();
                }
                if (!isset($graphic)) {
                    rtf::addParagraph("indicateur graphique : <i>non défini</i>");
                } else {
                    rtf::addParagraph("", $this->name);
                    rtf::addImage($graphic->path);
                }
                break;
        }
        if (strlen($this->description)>0) {
            rtf::addText("\n".$this->description);
        }

    }
    
    protected function getSessions() {
        return $this->evaluation->formSessions->as_array();
    }
    
    public function getVariablesInfo() {
        return $this->template->getVariablesInfo();
    }
    
    public function getVariablesList($numerical = false) {
        return $this->template->getVariablesList($numerical);
    }
    
    public function getVariablesIds($numerical = false, $simple = null) {
        return $this->template->getVariablesIds($numerical, $simple);
    }
    
    public function updateValues($values) {
        foreach ($values as $key=>$value) {
            $this->$key = $value;
        }
    }
    
    public function setParentId($id) {
        $parentIdField = $this->parentId;
        $this->$parentIdField = $id;
    }
    
    public function copyTo($parentId, & $user, $parameters = null, $variables = array(), $initAccess = false) {
        if (isset($parameters['indicator_model'])) {
            $indicatorModel = $parameters['indicator_model'];
        } else {
            $indicatorModel = $this->object_name;
        }
        $newIndicator = ORM::factory($indicatorModel);
        // copy information
        $data = $this->as_array();
        $doNotCopy = array('id','set_date','explanations', 'value_id', 'cached_value', 'cached_graphic_id');
        foreach ($doNotCopy as $key) {
            unset($data[$key]);
        }
        $newIndicator->updateValues($data);
        // initialize some piece of data      
        $newIndicator->evaluation_id = 0;
        $newIndicator->template_id = 0;
        $newIndicator->session_id = 0;
        $newIndicator->variable_id = $this->variable_id;
        // set parameters, if any
        if (isset($parameters)) {
            foreach ($parameters as $key=>$value) {
                $newIndicator->$key = $value;
            }
        }
        // set additional data
        $newIndicator->setParentId($parentId);
        $newIndicator->setOwner($user, false);
        $newIndicator->created = time();
        $newIndicator->order = $newIndicator->getNextOrder($parentId);
        // initAccess if requested
        if ($initAccess) {
            $newIndicator->inherit = 1;
            $newIndicator->view_id = 0;
            $newIndicator->edit_id = 0;
            $newIndicator->contribute_id = 0;
        }
        // deal with variable
        if (isset($variables) && isset($variables[$this->variable_id]))
            $newIndicator->variable_id = $variables[$this->variable_id];
        // save the new item
        $newIndicator->save();
        
        
        // deal with values, individuals and limits
        foreach ($this->limits as $limit) {
            $limit->copyTo($newIndicator);
        }
        foreach ($this->indicatorValues as $indicatorValue) {
            $indicatorValue->copyTo($newIndicator);
        }
        foreach ($this->individuals as $individual) {
            $individual->copyTo($newIndicator, $variables);
        }
        
    }
    
    public function belongsToTemplate() {
        return ($this->template_id > 0);
    }
    
   
}
?>