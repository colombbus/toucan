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

class FormSession_Controller extends Session_Controller {

    protected $dataName = "formSession";
    protected $sessionName = "formSession";
    protected $templateName = "formTemplate";
    protected $copyName = "formCopy";
    protected $controllerName = "forms";
    protected $templateControllerName = "FormTemplate_Controller";

    
    public function show($id) {
        // Special test where show could be called for a survey (comes from template usage)
        $this->loadData($id);
        if (isset($this->data->evaluation_id)&&($this->data->evaluation_id == 0)&&($this->sessionName!='survey')) {
            url::redirect("survey/show/$id");
        }
        parent::show($id);
        if ($this->data->isEditableBy($this->user, false) && !($this->data->isEditable())) {
            $this->template->content->info = Kohana::lang('session.not_editable');
        }
    }

    protected function mayBeEditedByPublic() {
        return true;
    }
}
?>