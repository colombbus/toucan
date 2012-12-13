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

abstract class Ajax_Controller extends Toucan_Controller {

    protected $dataName;
    protected $parentDataName;
    protected $controllerName;
    public $auto_render = TRUE;
    protected $view = null;


    public function __construct() {
        parent::__construct();
        if ($this->auto_render) {
            // Render the template immediately after the controller method
            Event::add('system.post_controller', array($this, '_render'));
        }

    }

    protected function displayError($message = false) {
        if ($message)
            die($message);
        else
            die('error');
    }

    public function delete($id) {
        // LOAD DATA
        $this->loadData($id);
        $this->ensureAccess(access::MAY_EDIT);
        // DELETION
        $this->data->delete();
        $this->auto_render = false;
    }

    public function reorder($id) {
        $childrenName = $this->dataName;
        $this->dataName = $this->parentDataName;
        $this->loadData($id);
        $this->ensureAccess(access::MAY_EDIT);
        if (!isset($_POST['data'])) {
            $this->displayError('no data provided');
        }
        parse_str($_POST['data']);
        $order = 1;
        foreach ($items as $item) {
            $dataItem = ORM::factory($childrenName, $item);
            $dataItem->order = $order;
            $dataItem->save();
            $order++;
        }
        $this->auto_render = false;
    }

   public function show($id, $new = false) {
        $this->loadData($id);
        $this->ensureAccess(access::MAY_VIEW);
        $item['title'] = $this->data->name;
        $item['content'] = $this->data->getDisplayableData(access::MAY_VIEW);
        $item['order'] = $this->data->order;
        $item['id'] = $id;
        $item['actions'] = $this->data->getItemActions($this->user);
        $this->view=new View("data/view_item");
        $this->view->newItem = $new;
        $this->view->item = $item;
        $this->view->isDraggable = $this->testAccess(access::MAY_EDIT);
        $this->view->dragUpdateRequired = $this->view->isDraggable;
    }

    public function create($parentId, $parameters=null) {
        // CREATE ITEM
        $newItem = ORM::factory($this->dataName);

        $parentItem = ORM::factory($this->parentDataName, $parentId);
        if (!isset($this->user)||!$this->user->mayEdit($parentItem)) {
            $this->displayError('restricted access');
        }

        // MANAGE FORM
        $formErrors= array();
        if (($post = $this->input->post())&&isset($post["form_create_$this->dataName"])) {
            // form submitted
            if ($newItem->validateCreation($post,$this->user,true)) {
                // data could be validated and saved
               $this->setMessage(Kohana::lang("$this->dataName.created"));
               if (isset($post['url_next'])) {
                   if (isset($post['url_next_with_id'])) {
                    url::redirect(urldecode($post['url_next']).$newItem->id);
                   } else {
                    url::redirect(urldecode($post['url_next']));
                   }
               }
                else
                    url::redirect("$this->controllerName/show/$newItem->id/1");
            } else {
                // errors while trying to validate creation
                $formErrors = $newItem->getErrors("form_errors");
                // populate values in order to retrieve input data
                $newItem->setValues($post);
            }
        }
        // DATA
        $creationData = $newItem->getCreationData($this->access, $this->user, $parameters);

        if (isset($parameters)) {
            foreach ($parameters as $key=>$value) {
                $creationData[] = array ('type' => 'hidden','name' => $key, 'value' => $value);
            }
        }
        $creationData[] = array('type' => 'hidden', 'name' => 'order', 'value'=>$newItem->getNextOrder($parentId));

        // TEMPLATE
        $item = array();
        $item['content'] = $creationData;
        $this->view=new View("data/new_item");
        $this->view->formId = "form_create_".$this->dataName;
        $this->view->item = $item;
        $this->view->errors = $formErrors;
        $this->view->cancel = "button.cancel";
        $this->view->save = "button.save";
        if (method_exists($newItem, "getConditional")) {
            $this->view->conditional = $newItem->getConditional();
        }
    }

    public function edit($id) {
        // LOAD DATA
        $this->loadData($id);
        $this->ensureAccess(access::MAY_EDIT);

        // MANAGE FORM
        $formErrors= array();
        if (($post = $this->input->post())&&isset($post["form_edit_$this->dataName"])) {
            if ($this->data->validateEdition($post,$this->user, true)) {
                // data could be validated and saved
                $this->setMessage(Kohana::lang("$this->dataName.edited"));
                url::redirect("$this->controllerName/show/$id");
            } else {
                // errors when trying to validate data
                $formErrors = $this->data->getErrors("form_errors");
                // populate values in order to retrieve input data
                $this->data->setValues($post);
            }
        }

        // TEMPLATE
        $this->view=new View("data/edit_item");
        $this->view->formId = "form_edit_$this->dataName";;

        // DATA
        $item['content'] = $this->data->getEditableData(access::MAY_EDIT, $this->user);
        $item['id'] = $this->data->id;
        $this->view->item = $item;

        $this->view->errors = $formErrors;
        $this->view->cancel = "button.cancel";
        $this->view->save = "button.save";

        if (method_exists($this->data, "getConditional")) {
            $this->view->conditional = $this->data->getConditional();
        }
   }

    protected function getItemActions() {
        $itemActions = array();
        $itemActions[] = array('function'=>"displayItem", 'text'=>$this->dataName.".display");
        if ($this->testAccess(access::MAY_EDIT)) {
            $itemActions[] = array('function'=>"editItem", 'text'=>$this->dataName.".edit");
            $itemActions[] = array('function'=>"deleteItem", 'text'=>$this->dataName.".delete");
        }
        return $itemActions;
    }

    /**
     * Render the loaded template.
     */
    public function _render() {
        if ($this->auto_render) {
            // Render the template when the class is destroyed
            $this->view->render(TRUE);
        }
    }


}
?>