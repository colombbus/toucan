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

class AxIndicator_Controller extends Ajax_Controller {

    protected $dataName = "indicator";
    protected $parentDataName = "evaluation";
    protected $controllerName = "axIndicator";
    protected $categoryName = "category";

    public function reorder($id, $category = false) {
        if ($category) {
            $oldDataName = $this->dataName;
            $this->dataName = $this->categoryName;
            $this->loadData($id);
            if (!$this->data->isRecapitulative()) {
                // category is not recapitulative: we deal with specific category order
                $this->ensureAccess(access::MAY_EDIT);
                if (!isset($_POST['data'])) {
                    $this->displayError('no data provided');
                }
                parse_str($_POST['data']);
                $categoryObject = ORM::factory($this->categoryName, $id);
                $categoryObject->setIndicators($items);
                $this->auto_render = false;
                return;
            } else {
                // category is recapitulative: manage general order
                $this->dataName = $oldDataName;
                $parent = $this->data->getParent();
                $id = $parent->id;
            }
        }
        parent::reorder($id);
    }
    
    public function fetch($id, $categoryId = null) {
        $this->dataName = $this->parentDataName;
        $this->loadData($id);
        $this->ensureAccess(access::MAY_VIEW);
        if (isset($categoryId))
            $sessionPrefix = "FETCH_{$this->parentDataName}_{$id}_{$categoryId}";
        else
            $sessionPrefix = "FETCH_{$this->parentDataName}_{$id}";
        $indicatorIds = $this->session->get($sessionPrefix."_ids",array());
        $indicatorCurrent = $this->session->get($sessionPrefix."_current",0);
        $this->view=new View("indicator/fetch_items");
        $this->view->isDraggable = $this->session->get($sessionPrefix."_draggable",false);
        $this->view->showContent = true;
        if ($indicatorCurrent>=count($indicatorIds)) {
            $this->view->noItems = true;
        } else {
            $updateCount = Kohana::config("toucan.items_per_fetch");
            $indicators = $this->data->getDisplayableIndicators($this->user, $indicatorIds, $indicatorCurrent, $updateCount);
            $this->view->items = $indicators;
            $this->session->set_flash($sessionPrefix."_current",$indicatorCurrent+$updateCount);
            $this->session->keep_flash();
        }
    }
    
    public function show($id, $new = false) {
        $this->loadData($id);
        $this->ensureAccess(access::MAY_VIEW);
        $this->view=new View("data/view_item");
        $this->view->newItem = $new;
        $this->view->item = $this->data->getDisplayableItemData($this->user);
        $this->view->isDraggable = $this->testAccess(access::MAY_EDIT);
        $this->view->dragUpdateRequired = $this->view->isDraggable;
        $this->view->showContent = true;
    }

    
    public function duplicate($evaluationId, $categoryId, $id) {
        $previousDataName = $this->dataName;
        $this->dataName = $this->parentDataName;
        $this->loadData($evaluationId);
        $this->ensureAccess(access::MAY_EDIT);
        $this->dataName = $previousDataName;
        $this->loadData($id);
        if ($categoryId > 0) {
            $category = ORM::factory($this->categoryName, $categoryId);
            if (!isset($category) || $category->isRecapitulative()) {
                $categoryId = null;
            }
        } else {
            $categoryId = null;
        }
        $newIndicator = $this->data->duplicate($this->user, $categoryId, true);
        $this->show($newIndicator->id, true);
    }
    
}
?>