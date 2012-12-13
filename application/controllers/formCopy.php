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

class FormCopy_Controller extends Copy_Controller {

    protected $dataName = "formCopy";
    protected $sessionName = "formSession";
    protected $controllerName = "forms";
    protected $publicControllerName = "public";
    protected $type = 1;
	
    public function create($sessionId) {
        parent::create($sessionId);
        $item = ORM::factory($this->dataName);
        $this->template->content->questionConditionals = $item->getConditionals();
        if ($item->getRequiredState())
            $this->template->content->requiredText = Kohana::lang('formCopy.required_text');
    }

    public function edit($id) {
        parent::edit($id);
        $this->template->content->questionConditionals = $this->data->getConditionals();
        if ($this->data->getRequiredState())
            $this->template->content->requiredText = Kohana::lang('formCopy.required_text');
    }


}
?>