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

class FormSession_Model extends Session_Model {
    protected $has_many = array('formCopies','indicators');
    protected $sessionType = 1;
    protected $templateName = "formTemplate";
    protected $instanceName = "formInstance1";
    protected $sessionName = "formSession";
    protected $copyName = "formCopy";
    protected $templateIndicatorModel = "formTemplateIndicator";

    public function mayBeEditedByPublic() {
        return true;
    }

    public function delete() {
        // First: delete all corresponding copies
        if ($this->loaded) {
            $copies = $this->formCopies;
            foreach ($copies as $copy) {
                $copy->delete();
            }
        }

        // Second: delete all corresponding indicators
        if ($this->loaded) {
            $indicators = $this->indicators;
            foreach ($indicators as $indicator) {
                $indicator->delete();
            }
        }

        // Third: delete the element itself
        parent::delete();
    }


    public function getCopies() {
        return $this->formCopies;
    }

}
?>