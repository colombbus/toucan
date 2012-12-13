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

class FormTemplateIndicator_Model extends TemplateIndicator_Model implements Ajax_Model {

    protected $table_name = "indicators";
    protected $accessParent = "template";
    protected $controllerName = "formTemplateIndicator";
    
    public function getCreationData($access, & $user, & $parameters = null) {
        $data = parent::getCreationData($access, $user, $parameters );
        $this->hideUnnecessaryData($data);
        return $data;
    }

    public function getEditableData($access, & $user) {
        $data = parent::getEditableData($access, $user);
        $this->hideUnnecessaryData($data);
        return $data;
    }
    
    public function getDisplayableData($access, & $user = null) {
        $data = parent::getDisplayableData($access, $user);
        $this->hideUnnecessaryData($data);
        return $data;
    }

    protected function hideUnnecessaryData(& $data) {
        foreach($data as $key=>$value) {
            $hide = false;
            if (isset($value['name'])) {
                if ($value['name'] == 'inherit_view')
                    $hide = true;
                if ($value['name'] == 'inherit_edit')
                    $hide = true;
                if ($value['name'] == 'view_id')
                    $hide = true;
                if ($value['name'] == 'edit_id')
                    $hide = true;
                if ($value['name'] == 'contribute_id')
                    $hide = true;
                if ($value['name'] == 'inherit')
                    $hide = true;
            }
            if ($hide) {
                $data[$key]['type'] = 'hidden';
                if (isset($data[$key]['label']))
                    unset($data[$key]['label']);
                if (isset($data[$key]['values']))
                    unset($data[$key]['values']);
                if (isset($data[$key]['required']))
                    unset($data[$key]['required']);
            }
        }
    }
    
}
?>