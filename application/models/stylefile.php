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

class Stylefile_Model extends File_Model {

    protected $ignored_columns = array('style_id', 'contents', 'oldName', 'upload', 'overwrite');
    protected $editableExtensions = array('tpl', 'css');
    protected $allowedExtensions = array('tpl', 'css', 'jpg', 'png', 'jpeg', 'gif', 'bmp');
    protected $has_and_belongs_to_many = array('styles');

    public function validateEdition(array & $array,& $user, $save = FALSE) {
        $result = parent::validateEdition($array, $user, $save);
        if ($result) {
            if (isset ($array['style_id'])) {
                $style = ORM::factory('style', $array['style_id']);
                $style->add($this);
            }
        }
        return $result;
    }

    public function validateUpload(array &$array, &$user, $save = FALSE) {
        $result = parent::validateUpload($array, $user, $save);
        if ($result) {
            if (isset ($array['style_id'])) {
                $style = ORM::factory('style', $array['style_id']);
                $style->updateFiles();
            }
        }
        return $result;
    }
    
    public function isEditableBy(& $user) {
        if ($this->loaded) {
            $styles = $this->styles;
            if (isset ($styles)) {
                $firstStyle = $styles->current();
                if (isset($firstStyle))
                    return $firstStyle->isEditableBy($user);
            }
        }
        return false;
    }
    
}
?>