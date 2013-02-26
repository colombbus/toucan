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
    
}
?>