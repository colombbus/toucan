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

class AxTemplateIndividual_Controller extends Ajax_Controller {

    protected $dataName = "templateIndividual";
    protected $parentDataName = "templateIndicator";
    protected $controllerName = "axTemplateIndividual";

    public function create($indicatorId) {
        parent::create($indicatorId, array('template_indicator_id'=>$indicatorId));
    }

    public function show($id, $new = false) {
        parent::show($id, $new);
        $this->view->isDraggable = false;
    }

}
?>