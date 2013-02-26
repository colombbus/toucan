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

class Indicator_Controller extends DataPage_Controller {

    protected $dataName = "indicator";
    protected $context = array();
    protected $parentName = "evaluation";
    protected $parentIdName = "evaluation_id";
    protected $parentIdField = "evaluationId";
    protected $controllerName = "indicator";
    protected $parentControllerName = "evaluation";
    

    protected function controlAccess($action) {
        switch($action) {
            case 'CREATE' :
                $this->ensureAccess(access::MAY_EDIT, $this->getParent());
                break;
            case 'EDIT' :
            case 'EDIT_CALCULATION' :
            case 'EDIT_GRAPHIC' :
            case 'LIMITS' :
            case 'RESET' :
            case 'CATEGORIES' :
                $this->ensureAccess(access::MAY_EDIT);
                break;
            case 'SHOW' :
            case 'POPULATION' :
            case 'CALCULATION' :
            case 'GRAPHIC' :
            case 'VALUES' :
                $this->ensureAccess(access::MAY_VIEW);
                break;
            case 'SET' :
                $this->ensureAccess(access::MAY_CONTRIBUTE);
                break;
            default :
                $this->ensureAccess(access::ADMIN);
                break;
        }
    }

    public function edit($id) {
        parent::edit($id);
        $this->createConditions();
    }

    protected function createConditions($allTriggers = true) {
        $conditional = array();
        if ($allTriggers) {
            $conditional[] = array('trigger'=>'type', 'triggered'=>'session_id','values'=>array(Indicator_Model::TYPE_AUTOMATIC_GRAPHIC,Indicator_Model::TYPE_AUTOMATIC_NUMERICAL));
            $conditional[] = array('trigger'=>'type', 'triggered'=>'contribute_id','value'=>Indicator_Model::TYPE_MANUAL);
        }
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'inherit_view');
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'inherit_edit');
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'view_id','reverse'=>true);
        $conditional[] = array('trigger'=>'inherit', 'triggered'=>'edit_id','reverse'=>true);
        $this->template->content->conditional = $conditional;
    }
    
    public function reset($id) {
        $this->loadData($id);
        $this->controlAccess('RESET');
        if ($this->data->type != Indicator_Model::TYPE_MANUAL) {
            $this->data->clearCache();
            if ($this->data->type == Indicator_Model::TYPE_AUTOMATIC_NUMERICAL)
                url::redirect($this->controllerName."/calculation/".$id);
            else
                url::redirect($this->controllerName."/graphic/".$id);
        }
    }

    public function values($id) {
        $this->loadData($id);
        $this->controlAccess('VALUES');

        $values = $this->data->getDisplayableValues($this->user);

        $this->template->content=new View('data/view_items');

        $this->template->content->items = $values;

        if (sizeof($values)==0) {
            $this->template->content->noItems = "indicatorValue.no_item";
        }

        $this->template->content->mayEdit = $this->testAccess(access::MAY_EDIT);
        $this->template->content->isDraggable = $this->testAccess(access::MAY_EDIT);
        $this->template->content->editUrl = "axValue/edit/";
        $this->template->content->displayUrl = "axValue/show/";
        $this->template->content->deleteUrl = "axValue/delete/";
        $this->template->content->reorderUrl = "axValue/reorder/".$id;
        $this->template->content->addUrl = "axValue/create/".$id;
        $this->template->content->confirmDeletion = "indicatorValue.delete_confirm";
        $this->template->content->alreadyEditing = "indicatorValue.already_editing";
        $this->template->content->hideItems = "indicatorValue.hide_all";
        $this->template->content->showItems = "indicatorValue.show_all";

        $this->setPageInfo('VALUES');
    }

    public function population($id, $changeOperator = false) {
        // LOAD DATA
        $this->loadData($id);

        // CONTROL ACCESS
        $this->controlAccess('POPULATION');
        if (($changeOperator)&&$this->testAccess(access::MAY_EDIT)) {
            $this->data->changeOperator();
        }

        $individuals = $this->data->getDisplayableIndividuals($this->user);

        $this->template->content=new View('data/view_items');

        $this->template->content->items = $individuals;

        if (sizeof($individuals)==0) {
            $this->template->content->noItems = "individual.no_item";
        }

        $this->template->content->mayEdit = $this->testAccess(access::MAY_EDIT);
        $this->template->content->isDraggable = false;
        $this->template->content->displayUrl = "axIndividual/show/";
        $this->template->content->editUrl = "axIndividual/edit/";
        $this->template->content->addUrl = "axIndividual/create/".$this->data->id;
        $this->template->content->deleteUrl = "axIndividual/delete/";
        $this->template->content->confirmDeletion = "individual.delete_confirm";
        $this->template->content->alreadyEditing = "individual.already_editing";
        $this->template->content->info = sprintf(Kohana::lang('indicator.current_operator'),$this->data->getDisplayableOperator());
        $this->setPageInfo('POPULATION');
    }

    public function limits($id) {
        $this->loadData($id);
        $this->controlAccess('LIMITS');

        $limits = $this->data->getDisplayableLimits($this->user);

        $this->template->content=new View('data/view_items');

        $this->template->content->items = $limits;

        if (sizeof($limits)==0) {
            $this->template->content->noItems = "limit.no_item";
        }

        $this->template->content->mayEdit = $this->testAccess(access::MAY_EDIT);
        $this->template->content->editUrl = "axLimit/edit/";
        $this->template->content->displayUrl = "axLimit/show/";
        $this->template->content->deleteUrl = "axLimit/delete/";
        $this->template->content->addUrl = "axLimit/create/".$id;
        $this->template->content->confirmDeletion = "limit.delete_confirm";
        $this->template->content->alreadyEditing = "limit.already_editing";
        $this->template->content->addItem = "limit.add";
        $this->template->content->showContent = true;

        $this->setPageInfo('LIMITS');
    }

    public function calculation($id) {
        $this->loadData($id);
        $this->controlAccess('CALCULATION');
        $this->template->content=new View('data/display');
        $this->template->content->data = $this->data->getDisplayableCalculation($this->access);
        // PAGE INFOS
        $this->setPageInfo('CALCULATION');
    }

    public function editCalculation($id, $parameters = null) {
        $this->loadData($id);
        $this->controlAccess('EDIT_CALCULATION');

        // MANAGE FORM
        $formErrors= array();
        if (($post = $this->input->post())&&isset($post["form_edit_calculation"])) {
            if ($this->data->validateCalculationEdition($post,$this->user, true)) {
                // data could be validated and saved
                if (isset($post['url_next'])) {
                    if (isset($post['url_next_with_id'])) {
                        url::redirect(urldecode($post['url_next']).$id);
                    } else {
                        url::redirect(urldecode($post['url_next']));
                   }
                } else {
                    $this->setMessage(Kohana::lang("calculation.message_edited"));
                    url::redirect($this->controllerName."/calculation/$id");
                }
            } else {
                // errors when trying to validate data
                $formErrors = $this->data->getErrors("form_errors");
                // populate values in order to retrieve input data
                $this->data->setValues($post);
            }
        }

        // TEMPLATE
        $this->template->content=new View('data/edit');
        $this->template->content->formId="form_edit_calculation";

        $this->template->content->errors = $formErrors;

        $data = $this->data->getEditableCalculation();
        
        if (isset($parameters)) {
            foreach ($parameters as $key=>$value) {
                $data[] = array ('type' => 'hidden','name' => $key, 'value' => $value);
            }
        }
        
        // DATA
        $this->template->content->data = $data;

        $conditional = array();
        $conditional[] = array('trigger'=>'calculation_id', 'triggered'=>'variable_id','values'=>Calculation_Model::getIdsWithVariable());
        $this->template->content->conditional = $conditional;

        $this->setPageInfo('EDIT_CALCULATION');
    }

    public function graphic($id) {
        $this->loadData($id);
        $this->controlAccess('GRAPHIC');
        $this->template->content=new View('data/display');
        try {
            $this->template->content->data = $this->data->getDisplayableGraphic();
        } catch (Exception $e) 
        {
            $this->clearMessage();
            $this->setErrorMessage(Kohana::lang($e->getMessage()));
            $this->template->content->data = $this->data->getDisplayableGraphic(false);
        }
        // PAGE INFOS
        $this->setPageInfo('GRAPHIC');
    }

    public function editGraphic($id , $parameters = null) {
        $this->loadData($id);
        $this->controlAccess('EDIT_GRAPHIC');

        // MANAGE FORM
        $formErrors= array();
        if (($post = $this->input->post())&&isset($post["form_edit_graphic"])) {
            if ($this->data->validateGraphicEdition($post,$this->user, true)) {
                // data could be validated and saved
                if (isset($post['url_next'])) {
                    if (isset($post['url_next_with_id'])) {
                        url::redirect(urldecode($post['url_next']).$id);
                    } else {
                        url::redirect(urldecode($post['url_next']));
                   }
                } else {
                    $this->setMessage(Kohana::lang("graphic.message_edited"));
                    url::redirect($this->controllerName."/graphic/$id");
                }
            } else {
                // errors when trying to validate data
                $formErrors = $this->data->getErrors("form_errors");
                // populate values in order to retrieve input data
                $this->data->setValues($post);
            }
        }

        // TEMPLATE
        $this->template->content=new View('data/edit');
        $this->template->content->formId="form_edit_graphic";

        $this->template->content->errors = $formErrors;

        $data = $this->data->getEditableGraphic();
        
        if (isset($parameters)) {
            foreach ($parameters as $key=>$value) {
                $data[] = array ('type' => 'hidden','name' => $key, 'value' => $value);
            }
        }

        // DATA
        $this->template->content->data = $data;

        $conditional = array();
        $conditional[] = array('trigger'=>'graphic_id', 'triggered'=>'graphic_x_axis','value'=>Graphic_Model::PIE_CHART, 'reverse'=>true);
        $conditional[] = array('trigger'=>'graphic_id', 'triggered'=>'graphic_y_axis','value'=>Graphic_Model::PIE_CHART, 'reverse'=>true);
        $this->template->content->conditional = $conditional;


        $this->setPageInfo('EDIT_GRAPHIC');
    }

    public function create($parentId, $type, $id = null) {
        $url_next = "";
        $this->context['type'] = $type;
        switch($type) {
            case Indicator_Model::TYPE_MANUAL:
                $url_next = $this->controllerName."/createValues/";
                break;
            case Indicator_Model::TYPE_AUTOMATIC_GRAPHIC:
                $url_next = $this->controllerName."/createGraphic/";
                break;
            case Indicator_Model::TYPE_AUTOMATIC_NUMERICAL:
                $url_next = $this->controllerName."/createCalculation/";
                break;
        }
        if (!isset($id)) {
            $this->context[$this->parentIdField] = $parentId;
            parent::create(array($this->parentIdName=>$parentId, 'type'=>$type, 'url_next'=>$url_next, 'url_next_with_id'=>1));
            $this->createConditions(false);
        } else {
            // LOAD DATA
            $this->loadData($id);

            // CONTROL ACCESS
            $this->controlAccess('EDIT');

            // MANAGE FORM
            $formErrors= array();
            if (($post = $this->input->post())&&isset($post["form_edit_$this->dataName"])) {
                if ($this->data->validateEdition($post,$this->user, true)) {
                    // data could be validated and saved
                    $this->setMessage(Kohana::lang("$this->dataName.message_edited"));
                    url::redirect($url_next.$id);
                } else {
                    // errors when trying to validate data
                    $formErrors = $this->data->getErrors("form_errors");
                    // populate values in order to retrieve input data
                    $this->data->setValues($post);
                }
            }

            // TEMPLATE
            $this->template->content=new View('data/edit');
            $this->template->content->formId="form_edit_$this->dataName";


            $this->template->content->errors = $formErrors;

            // DATA
            $editionData = $this->data->getCreationData($this->access, $this->user);
            $this->template->content->data = $editionData;
            // PAGE INFOS
            $this->setPageInfo('CREATE');
            $this->createConditions(false);
        }
    }
    
    public function createValues($id) {
        $message = $this->getMessage();
        $this->clearMessage();
        $this->values($id);
        $this->setPageInfo('CREATE_VALUES');
    }

    public function createGraphic($id) {
        $message = $this->getMessage();
        $this->clearMessage();
        $this->editGraphic($id, array('url_next'=>$this->controllerName.'/createPopulation/'.$id));
        $this->setPageInfo('CREATE_GRAPHIC');
    }

    public function createCalculation($id) {
        $message = $this->getMessage();
        $this->clearMessage();
        $this->editCalculation($id, array('url_next'=>$this->controllerName.'/createPopulation/'.$id));
        $this->setPageInfo('CREATE_CALCULATION');
    }
    
    public function createPopulation($id, $changeOperator = false) {
        $this->population($id, $changeOperator);
        $this->context['type'] = $this->data->type;
        $this->setPageInfo('CREATE_POPULATION');
    }

    public function createLimits($id) {
        $this->limits($id);
        $this->setPageInfo('CREATE_LIMITS');
    }

    public function createCategories($id) {
        $this->loadData($id);
        $parent = $this->getParent();
        $this->categories($id, $this->parentControllerName.'/indicators/'.$parent->id);
        $this->setPageInfo('CREATE_CATEGORIES');
    }
    
    public function created($id) {
        $this->loadData($id);
        $parent = $this->getParent();
        $this->setMessage(Kohana::lang("$this->dataName.message_created"));
        url::redirect($this->parentControllerName.'/indicators/'.$parent->id);
    }
    
    public function cancelCreation($id) {
        // LOAD DATA
        $this->loadData($id);

        // CONTROL ACCESS
        $this->controlAccess('DELETE');

        $parent = $this->getParent();
        
        // DELETION
        $this->data->delete();

        url::redirect($this->parentControllerName."/indicators/$parent->id");    
    }
    
    public function createStart($parentId) {
        $this->context[$this->parentIdField] = $parentId;
        // CONTROL ACCESS
        $this->controlAccess('CREATE');
        
        // Check if there are form sessions attached to the parent
        $parent = $this->getParent();
        if (!$parent->hasForms()) {
            // if no forms, jump directly to manual indicator creation
            return $this->create($parentId, Indicator_Model::TYPE_MANUAL);
        }
        
        // TEMPLATE
        $this->template->content=new View('select');

        $choices = array();
        $choices[] = array('text'=>Kohana::lang($this->controllerName.'.create_manual'), 'link'=>$this->controllerName.'/create/'.$parentId.'/'.Indicator_Model::TYPE_MANUAL, 'image'=>Kohana::config('toucan.images_directory')."/values.png"); 
        $choices[] = array('text'=>Kohana::lang($this->controllerName.'.create_graphic'), 'link'=>$this->controllerName.'/create/'.$parentId.'/'.Indicator_Model::TYPE_AUTOMATIC_GRAPHIC, 'image'=>Kohana::config('toucan.images_directory')."/graphic.png"); 
        $choices[] = array('text'=>Kohana::lang($this->controllerName.'.create_calculation'), 'link'=>$this->controllerName.'/create/'.$parentId.'/'.Indicator_Model::TYPE_AUTOMATIC_NUMERICAL, 'image'=>Kohana::config('toucan.images_directory')."/calculation.png"); 
        
        $this->template->content->choices  = $choices;
        $this->template->content->selectType = "select_session_creation";
                
        // PAGE INFOS
        $this->setPageInfo('CREATE_START');
    }
    
    public function categories($id, $url=null) {
        $this->controlAccess('CATEGORIES');

        $formErrors= array();
        $error = false;

        $this->loadData($id);

        $indicator = $this->data;

        if (($post = $this->input->post())&&isset($post['form_indicator_categories'])) { // form submitted
            if ($indicator->validateCategorySelection($post,true)) { // categories could be validated and saved
                $this->setMessage(Kohana::lang('indicator.message_edited'));
                if (!isset($url))
                    $url = $this->controllerName.'/show/'.$id;
                url::redirect($url);
            } else {
                $formErrors = $indicator->getErrors("form_errors");
                $error = true;
            }
        }

        $this->template->content=new View('data/edit');
        $this->template->content->formId = 'form_indicator_categories';
        $data = $indicator->getCategoriesEditableData($this->user);

        // Set previous values in case of an error
        if (($error)&&(isset($post['category']))&&(is_array($post['category']))) {
            $previous = $post['category'];
            for($i=0;$i<count($data);$i++) {
                if (in_array($data[$i]['value'], $previous))
                    $data[$i]['checked'] = 1;
                else
                    $data[$i]['checked'] = 0;
            }
        }
        $this->setPageInfo('CATEGORIES');
        $this->template->content->data = $data;
        $this->template->content->errors = $formErrors;
    }

    
    public function set($id) {
        $this->loadData($id);
        $this->controlAccess('SET');

        // MANAGE FORM
        $formErrors= array();
        if (($post = $this->input->post())&&isset($post["form_set_indicator"])) {
            if ($this->data->validateSet($post,$this->user, true)) {
                // data could be validated and saved
                $this->setMessage(Kohana::lang("indicator.message_set"));
                $parentId = $this->parentIdName;
                url::redirect($this->parentControllerName."/indicators/".$this->data->$parentId);
            } else {
                // errors when trying to validate data
                $formErrors = $this->data->getErrors("form_errors");
                // populate values in order to retrieve input data
                $this->data->setValues($post);
            }
        }

        // TEMPLATE
        $this->template->content=new View('data/edit');
        $this->template->content->formId="form_set_indicator";

        $this->template->content->errors = $formErrors;

        // DATA
        $this->template->content->data = $this->data->getSetData();

        $this->setPageInfo('SET');
    }
    
    public function delete($id) {
        // LOAD DATA
        $this->loadData($id);

        // CONTROL ACCESS
        $this->controlAccess('DELETE');

        // PARENT ID
        $parentIdName = $this->parentIdName;
        $parentId = $this->data->$parentIdName;
        
        // DELETION
        $this->data->delete();
        
        $this->setMessage(Kohana::lang("$this->dataName.message_deleted"));
        url::redirect($this->parentControllerName."/indicators/$parentId");        
    }

    
    
    
    protected function setHeaders($action) {
        $headers = array();
        switch ($action) {
            case 'OWNER' :
                $headers[0] = array('text'=>'user.firstname', 'name'=>'firstname');
                $headers[1] = array('text'=>'user.name','name'=>'name');
                $headers[2] = array('text'=>'user.username','name'=>'username');
                break;
        }
        $this->template->content->headers = $headers;
    }

    protected function setActions($action) {
        $actions = array();
        $actions_back = array();
        $indicator = $this->data;
        $parent = $this->getParent();
        switch ($action) {
            case 'SET' :
                $actions_back[] = array('type' => 'button','text' => 'button.cancel','url' => $this->parentControllerName.'/indicators/'.$parent->id);
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'CREATE_START' :
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                break;
            case 'CREATE' :
                if (isset($indicator))
                    $actions_back[] = array('type' => 'button','text' => 'button.cancel', 'url'=>$this->controllerName.'/cancelCreation/'.$indicator->id);
                else
                    $actions_back[] = array('type' => 'button','text' => 'button.cancel', 'url'=>$this->parentControllerName.'/indicators/'.$parent->id);
                $actions[] = array('type' => 'submit','text' => 'button.step_forward');
                break;
            case 'CREATE_VALUES' :
                $actions_back[] = array('type' => 'button','text' => 'indicatorValue.add','js' => 'addItem()');
                if ($parent->hasCategories($this->user))
                    $actions[] = array('type' => 'button','text' => 'button.step_forward', 'url'=>$this->controllerName.'/createCategories/'.$indicator->id);
                else
                    $actions[] = array('type' => 'button','text' => 'button.terminate', 'url'=>$this->controllerName.'/created/'.$indicator->id);
                break;
            case 'CREATE_POPULATION' :
                $actions_back[] = array('type' => 'button','text' => 'individual.add','js' => 'addItem()');
                $actions_back[] = array('type' => 'button','text' => 'indicator.change_operator','url' => $this->controllerName.'/createPopulation/'.$indicator->id.'/1');
                if ($this->context['type'] == Indicator_Model::TYPE_AUTOMATIC_NUMERICAL)
                    $actions[] = array('type' => 'button','text' => 'button.step_forward', 'url'=>$this->controllerName.'/createLimits/'.$indicator->id);
                else if ($parent->hasCategories($this->user))
                    $actions[] = array('type' => 'button','text' => 'button.step_forward', 'url'=>$this->controllerName.'/createCategories/'.$indicator->id);
                else
                    $actions[] = array('type' => 'button','text' => 'button.terminate', 'url'=>$this->controllerName.'/created/'.$indicator->id);
                break;
            case 'CREATE_GRAPHIC' :
                $actions_back[] = array('type' => 'button','text' => 'button.back','url'=>$this->controllerName.'/create/'.$parent->id.'/'.$indicator->type.'/'.$indicator->id);
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'CREATE_CALCULATION' :
                $actions_back[] = array('type' => 'button','text' => 'button.back','url'=>$this->controllerName.'/create/'.$parent->id.'/'.$indicator->type.'/'.$indicator->id);
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'CREATE_LIMITS' :
                $actions_back[] = array('type' => 'button','text' => 'limit.add','js' => 'addItem()');
                if ($parent->hasCategories($this->user))
                    $actions[] = array('type' => 'button','text' => 'button.step_forward', 'url'=>$this->controllerName.'/createCategories/'.$indicator->id);
                else
                    $actions[] = array('type' => 'button','text' => 'button.terminate', 'url'=>$this->controllerName.'/created/'.$indicator->id);
                break;
            case 'CREATE_CATEGORIES' :
                $actions[] = array('type' => 'submit','text' => 'button.terminate');
                break;
            case 'EDIT' :
                $actions_back[] = array('type' => 'button','text' => 'button.cancel','url' => $this->controllerName.'/show/'.$indicator->id);
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'VALUES' :
                if ($this->testAccess(access::MAY_EDIT)) {
                    $actions[] = array('type' => 'button','text' => 'indicatorValue.add','js' => 'addItem()');
                }
                break;
            case 'SHOW' :
                if ($this->testAccess(access::MAY_EDIT)) {
                    $actions[] = array('type' => 'button','text' => 'button.edit','url' => $this->controllerName.'/edit/'.$indicator->id);
                    $actions[] = array('type' => 'button_confirm','text' => 'button.delete','confirm' => 'indicator.delete_confirm','url' => $this->controllerName.'/delete/'.$indicator->id);
                }
                if ($this->testAdminAccess()) {
                    $actions[] = array('type' => 'button','text' => 'indicator.set_owner','url' => $this->controllerName.'/owner/'.$indicator->id);
                }
                break;
            case 'POPULATION' :
                if ($this->testAccess(access::MAY_EDIT)) {
                    $actions[] = array('type' => 'button','text' => 'individual.add','js' => 'addItem()');
                    $actions[] = array('type' => 'button','text' => 'indicator.change_operator','url' => $this->controllerName.'/population/'.$indicator->id.'/1');
                }
                break;
            case 'CALCULATION' :
                if ($this->testAccess(access::MAY_EDIT)) {
                   $actions[] = array('type' => 'button','text' => 'button.edit','url' => $this->controllerName.'/editCalculation/'.$indicator->id);
                   $actions[] = array('type' => 'button','text' => 'indicator.reset','confirm' => 'indicator.reset','url' => $this->controllerName.'/reset/'.$indicator->id);
                }
                break;
            case 'GRAPHIC' :
                if ($this->testAccess(access::MAY_EDIT)) {
                   $actions[] = array('type' => 'button','text' => 'button.edit','url' => $this->controllerName.'/editGraphic/'.$indicator->id);
                   $actions[] = array('type' => 'button','text' => 'indicator.reset','confirm' => 'indicator.reset','url' => $this->controllerName.'/reset/'.$indicator->id);
                }
                break;
            case 'EDIT_CALCULATION' :
                $actions_back[] = array('type' => 'button','text' => 'button.cancel','url' => $this->controllerName.'/calculation/'.$indicator->id);
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'EDIT_GRAPHIC' :
                $actions_back[] = array('type' => 'button','text' => 'button.cancel','url' => $this->controllerName.'/graphic/'.$indicator->id);
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
            case 'LIMITS' :
                if ($this->testAccess(access::MAY_EDIT)) {
                    $actions[] = array('type' => 'button','text' => 'limit.add','js' => 'addItem()');
                }
                break;
            case 'OWNER':
                $actions_back[] = array('type' => 'cancel','text' => 'button.cancel');
                break;
            case 'CATEGORIES':
                $actions[] = array('type' => 'submit','text' => 'button.save');
                break;
        }
        $tabs = array();
        if (isset ($indicator)&&substr($action, 0, 6) != 'CREATE') {
            $categories = $parent->hasCategories($this->user);
            $tabs[0] = array('text'=>'indicator.info', 'link'=>$this->controllerName.'/show/'.$indicator->id,'image'=>Kohana::config("toucan.images_directory")."/information.png");
            $index = 1;
            switch ($indicator->type) {
                case Indicator_Model::TYPE_MANUAL :
                    $tabs[$index++] = array('text'=>'indicator.values_header', 'link'=>$this->controllerName.'/values/'.$indicator->id, 'image'=>Kohana::config("toucan.images_directory")."/values.png");
                    if ($this->testAccess(Access::MAY_CONTRIBUTE)) {
                        $tabs[$index++] = array('text'=>'indicator.set', 'link'=>$this->controllerName.'/set/'.$indicator->id, 'image'=>Kohana::config("toucan.images_directory")."/set.png");
                    }
                    if ($categories && $this->testAccess(Access::MAY_EDIT))
                        $tabs[$index++] = array('text'=>'indicator.categories', 'link'=>$this->controllerName.'/categories/'.$indicator->id,'image'=>Kohana::config("toucan.images_directory")."/categories.png");
                    break;
                case Indicator_Model::TYPE_AUTOMATIC_NUMERICAL :
                    $tabs[$index++] = array('text'=>'indicator.calculation', 'link'=>$this->controllerName.'/calculation/'.$indicator->id, 'image'=>Kohana::config("toucan.images_directory")."/calculation.png");
                    $tabs[$index++] = array('text'=>'indicator.population', 'link'=>$this->controllerName.'/population/'.$indicator->id, 'image'=>Kohana::config("toucan.images_directory")."/population.png");
                    if ($this->testAccess(Access::MAY_EDIT)) {
                        $tabs[$index++] = array('text'=>'indicator.limits', 'link'=>$this->controllerName.'/limits/'.$indicator->id,'image'=>Kohana::config("toucan.images_directory")."/limits.png");
                        if ($categories)
                            $tabs[$index++] = array('text'=>'indicator.categories', 'link'=>$this->controllerName.'/categories/'.$indicator->id,'image'=>Kohana::config("toucan.images_directory")."/categories.png");
                    }
                    break;
                case Indicator_Model::TYPE_AUTOMATIC_GRAPHIC :
                    $tabs[$index++] = array('text'=>'indicator.graphic', 'link'=>$this->controllerName.'/graphic/'.$indicator->id,'image'=>Kohana::config("toucan.images_directory")."/graphic.png");
                    $tabs[$index++] = array('text'=>'indicator.population', 'link'=>$this->controllerName.'/population/'.$indicator->id,'image'=>Kohana::config("toucan.images_directory")."/population.png");
                    if ($categories && $this->testAccess(Access::MAY_EDIT))
                        $tabs[$index++] = array('text'=>'indicator.categories', 'link'=>$this->controllerName.'/categories/'.$indicator->id,'image'=>Kohana::config("toucan.images_directory")."/categories.png");
                    break;
            }
            switch ($action) {
                case 'EDIT' :
                case 'SHOW' :
                case 'OWNER' :
                    $tabs[0]['current'] = 1;
                    break;
                case 'VALUES' :
                case 'GRAPHIC' :
                case 'EDIT_GRAPHIC' :
                case 'CALCULATION' :
                case 'EDIT_CALCULATION' :
                    $tabs[1]['current'] = 1;
                    break;
                case 'SET' :
                case 'POPULATION' :
                    $tabs[2]['current'] = 1;
                    break;
                case 'LIMITS' :
                    $tabs[3]['current'] = 1;
                    break;
                case 'CATEGORIES' :
                    $tabs[$index-1]['current'] = 1;
                    break;
            }
        } 
        $this->template->content->actions = $actions;
        $this->template->content->actions_back = $actions_back;
        $this->template->content->tabs = $tabs;
    }

    protected function setPath($action) {
        $path = array();
        $parent = $this->getParent();
        if (isset($parent)) {
            $activity = $parent->activity;
            $path[] = array('text'=>sprintf(Kohana::lang("activity.main_title"), $activity->name), 'link'=>"activity/evaluations/$activity->id");
            $path[] = array('text'=>sprintf(Kohana::lang($this->parentControllerName.".main_title"), $parent->name), 'link'=>$this->parentControllerName."/indicators/$parent->id");
        }
        $this->template->content->path = $path;
    }

    protected function setDescription($action) {
        parent::setDescription($action);
        $parent = $this->getParent();
        if ($action != 'CREATE_VALUES'&&$action!='CREATE_START'&&$action!='CREATE'&&$action != 'CREATE_GRAPHIC'&&$action != 'CREATE_CALCULATION'&&$action != 'CREATE_POPULATION'&&$action != 'CREATE_LIMITS'){
            if (isset($this->data)&&($this->data->loaded)) {
                $this->template->content->title = sprintf(Kohana::lang("indicator.main_title"), $this->data->name);
            }
            $activity = $this->getActivity();
            if (isset($activity)&&$activity->logo_id >0)
                $this->template->content->title_logo = $activity->logo->path;
        } else {
            if ($parent->hasCategories($this->user))
                $categories = 1;
            else
                $categories = 0;
            if ($action=='CREATE') {
                switch ($this->context['type']) {
                    case Indicator_Model::TYPE_MANUAL:
                        $this->template->content->title_steps = array('max'=>2+$categories, 'current'=>1);
                        break;
                    case Indicator_Model::TYPE_AUTOMATIC_GRAPHIC:
                        $this->template->content->title_steps = array('max'=>3+$categories, 'current'=>1);
                        break;
                    case Indicator_Model::TYPE_AUTOMATIC_NUMERICAL:
                        $this->template->content->title_steps = array('max'=>4+$categories, 'current'=>1);
                        break;
                }
            } else if ($action=='CREATE_VALUES') {
                $this->template->content->title_steps = array('max'=>2+$categories, 'current'=>2);
                $this->helpTopic="values";
            } else if ($action=='CREATE_GRAPHIC') {
                $this->template->content->title_steps = array('max'=>3+$categories, 'current'=>2);
                $this->helpTopic="editGraphic";
            } else if ($action=='CREATE_CALCULATION') {
                $this->template->content->title_steps = array('max'=>4+$categories, 'current'=>2);
                $this->helpTopic="editCalculation";
            } else if ($action=='CREATE_POPULATION') {
                if ($this->context['type'] == Indicator_Model::TYPE_AUTOMATIC_NUMERICAL)
                    $this->template->content->title_steps = array('max'=>4+$categories, 'current'=>3);
                else 
                    $this->template->content->title_steps = array('max'=>3+$categories, 'current'=>3);
                $this->helpTopic="population";
            } else if ($action=='CREATE_LIMITS') {
                $this->template->content->title_steps = array('max'=>4+$categories, 'current'=>4);
                $this->helpTopic="limits";
            }
        }
        $this->template->content->title_icon = Kohana::config("toucan.images_directory")."/indicator.png";
        $this->template->content->pathType = "path_activity";
    }

    protected function getParent() {
        if (isset ($this->data)) {
            $parentName = $this->parentName;
            return $this->data->$parentName;
        } else if (isset ($this->context[$this->parentIdField])) {
            return ORM::factory($this->parentName, $this->context[$this->parentIdField]);
        }
        return null;
    }
    
    protected function getActivity() {
        $parent = $this->getParent();
        if (isset($parent)) {
            return $parent->activity;
        }
        return null;
    }


}
?>