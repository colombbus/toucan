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

class TemplateIndicatorValue_Model extends IndicatorValue_Model implements Ajax_Model {

    protected $table_name = "template_indicator_values";
    protected $belongs_to = array("templateIndicator");
    protected $foreign_key = array("templateIndicator"=>"template_indicator_id");
    
    
    public function validateEdition(array & $array,& $user, $save = FALSE) {
        $this->validation = Validation::factory($array)
            ->pre_filter('trim')
            ->add_rules('name', 'required', 'length[1,127]')
            ->add_callbacks('name', array($this, 'uniqueNameByIndicator'))
            ->add_rules('description', 'length[0,500]')
            ->add_rules('template_indicator_id', 'valid::numeric')
            ->add_rules('color_id', 'valid::numeric')
            ->add_rules('order', 'valid::numeric');
        return parent::validate($this->validation, $save);
    }
    
    
    public function getNextOrder($indicatorId) {
        $result = $this->db->query("SELECT max(`order`)+1 from template_indicator_values WHERE template_indicator_id = $indicatorId");
        $result->result(false,MYSQL_NUM);
        if ($result[0][0] === null)
            return 1;
        else
            return $result[0][0];
    }

    public function uniqueNameByIndicator(Validation $valid) {
        if (array_key_exists('name', $valid->errors()))
            return;
        if (isset ($valid->indicator_id)) {
            $escapedName = addslashes($valid->name);
            $indicatorId = $valid->template_indicator_id;
            if ($this->loaded) {
                $other = $this->db->query("SELECT id from ".$this->table_name." WHERE id != $this->id AND template_indicator_id = '$indicatorId' AND name = '$escapedName'");
            } else {
                $other = $this->db->query("SELECT id from ".$this->table_name." WHERE template_indicator_id = '$indicatorId' AND name = '$escapedName'");
            }
            if ($other->count() > 0) {
                $valid->add_error( 'name', 'uniqueName');
            }
        }
    }
    
    public function isEditableBy(& $user) {
        if ($this->loaded) {
            $indicator = $this->templateIndicator;
            return $indicator->isEditableBy($user);
        }
        return false;
    }

    
}