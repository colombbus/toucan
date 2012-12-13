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

abstract class Toucan_Model extends ORM {

    protected $validation = null;
    protected $accessParent = null;
    protected $accessHeirs = null;
    protected $valuesSet = false;

    public abstract function getCreationData($access, & $user, & $parameters = null);
    public abstract function getEditableData($access, & $user);
    public abstract function getDisplayableData($access,& $user =null);
    public abstract function validateEdition(array & $array,& $user, $save = FALSE);
    public abstract function validateCreation(array & $array,& $user, $save = FALSE);
    public abstract function count(& $filter , & $user, $constraintId = null);
    public abstract function getItems(& $filter,& $user,$offset = 0, $number = null, $constraintId = null);

    protected function hasOwner() {
        return (array_key_exists('owner',$this->belongs_to));
    }

    protected function hasView() {
        return (array_key_exists('view',$this->has_one));
    }

    protected function hasEdit() {
        return (array_key_exists('edit',$this->has_one));
    }

    protected function hasContribute() {
        return (array_key_exists('contribute',$this->has_one));
    }

    public function isViewableBy(& $user) {
        if (isset ($this->accessParent)) {
            if (!DataAccess::testAccess($this->getAccessParent(),$user,access::MAY_VIEW))
                return false;
        }
        if ($this->hasView()) {
            if ($this->inheritAccess()) {
                // Normally should return $this->getAccessParent()->isViewableBy($user), but this test has already been passed
                return true;
            } else {
                if ($this->hasOwner()) {
                    $ownerId = $this->owner->id;
                } else {
                    $ownerId = null;
                }
                return DataAccess::checkAccess($user, $this->getDisplayGroup(), $ownerId);
            }
        }
        return false;
    }

    public function isEditableBy(& $user) {
        if (isset ($this->accessParent)) {
            if (!DataAccess::testAccess($this->getAccessParent(),$user,access::MAY_VIEW))
                return false;
        }
        if ($this->hasEdit()) {
            if ($this->inheritAccess()) {
                return $this->getAccessParent()->isEditableBy($user);
            } else {
                $ownerId = $this->owner->id;
                return DataAccess::checkAccess($user, $this->getEditGroup(), $ownerId);
            }
        }
        return false;
    }

    public function isEditable() {
        return true;
    }

    public function mayBeContributedBy(& $user) {
        if (isset ($this->accessParent)) {
            if (!DataAccess::testAccess($this->getAccessParent(),$user,access::MAY_VIEW))
                return false;
        }
        if ($this->hasContribute()) {
            if ($this->hasOwner()) {
                $ownerId = $this->owner->id;
            } else {
                $ownerId = null;
            }
            $test = DataAccess::checkAccess($user, $this->contribute, $ownerId);
            return DataAccess::checkAccess($user, $this->contribute, $ownerId);
        }
        return false;
    }


    public function isOwner(& $user) {
        if ($this->hasOwner()&&isset($user)) {
            return ($this->owner_id == $user->id);
        }
        return false;
    }

    public function setOwner(& $user, $save = false) {
        if ($this->hasOwner()) {
            $this->owner_id = $user->id;
            if ($save)
                $this->save();
        }
    }

    public function getErrors($lang_file=null) {
        if (!isset($this->validation)) {
            return null;
        } else {
            if (isset($lang_file)) {
                return $this->validation->errors($lang_file);
            } else {
                return $this->validation->errors();
            }
        }
    }

    public function setValues(& $array) {
        $this->load_values($array);
        $this->valuesSet = true;
    }

    public function uniqueName($name) {
        $escapedName = addslashes($name);
        if ($this->loaded) {
            $other = $this->db->query("SELECT id from ".$this->table_name." WHERE id != $this->id AND name = '$escapedName'");
        } else {
            $other = $this->db->query("SELECT id from ".$this->table_name." WHERE name = '$escapedName'");
        }
        return !($other->count() > 0);
    }

    public function validDate($valid,$field) {
        if (array_key_exists($field, $valid->errors()))
            return;
        if (strlen($valid->$field)==0) {
            $valid->$field = NULL;
            return;
        }
        $translatedDate = Utils::date_str2db($valid->$field,Kohana::lang("calendar.format_check"));
        if ($translatedDate === FALSE)
            $valid->add_error($field, 'valid_date');
        else
            $valid->$field = $translatedDate;
    }
    
    public function endAfterStart($valid) {
        if (array_key_exists("end_date", $valid->errors()))
            return;
        if ((strlen($valid->end_date)==0)||(strlen($valid->start_date)==0))
            return;
        if (!Utils::compareDates($valid->start_date, $valid->end_date))
            $valid->add_error("end_date", 'before_start');
    }

    public function keepOldValue($name) {
        if (strlen($name)==0) {
            if (isset($this->$name))
                return $this->$name;
        }
        return $name;
    }

    public function getDisplayGroupInfo() {
        if ($this->inheritAccess()) {
            // find value from inherited element;
            return $this->getAccessParent()->getDisplayGroupInfo();
        } else {
            // find value from the element itself
            if ($this->view_id == Group_Model::SPECIAL_GROUP_OWNER) {
                return array('value'=>$this->owner->fullName, 'link'=> '/user/profile/'.$this->owner_id);
            } else {
                return array('value'=> $this->view->name, 'link'=> '/group/show/'.$this->view_id);
            }
        }
    }

    public function getDisplayGroupId() {
        if ($this->inheritAccess()) {
            // find id from inherited element;
            return $this->getAccessParent()->getDisplayGroupId();
        } else {
            // find id from the element itself
            return $this->view_id;
        }
    }

    public function getDisplayGroup() {
        return ORM::factory('group',$this->getDisplayGroupId());
    }

    public function getEditGroupInfo() {
        if ($this->inheritAccess()) {
            // find value from inherited element;
            return $this->getAccessParent()->getEditGroupInfo();
        } else {
            // find value from the element itself
            if ($this->edit_id == 0) {
                return array('value'=> $this->owner->fullName, 'link'=> '/user/profile/'.$this->owner_id);
            } else {
                return array('value'=> $this->edit->name, 'link'=> '/group/show/'.$this->edit_id);
            }
        }
    }

    public function getEditGroup() {
        return ORM::factory('group',$this->getEditGroupId());
    }

    public function getEditGroupId() {
        if ($this->inheritAccess()) {
            // find id from inherited element;
            return $this->getAccessParent()->getEditGroupId();
        } else {
            // find id from the element itself
            return $this->edit_id;
        }
    }

    public function getContributeGroupInfo() {
        if ($this->contribute_id == 0) {
            return array('value'=> $this->owner->fullName, 'link'=> '/user/profile/'.$this->owner_id);
        } else {
            return array('value'=> $this->contribute->name, 'link'=> '/group/show/'.$this->contribute_id);
        }
    }

    public function getAccessOwnerId() {
        if ($this->inheritAccess()) {
            // find id from inherited element;
            return $this->getAccessParent()->getAccessOwnerId();
        } else {
            // find id from the element itself
            return $this->owner_id;
        }
    }


    protected function addEditableGroups(& $data, $prefix) {
        $rawGroups = Group_Model::getProtectedGroups();
        $protectedGroups = array();
        $protectedGroups[0] = Kohana::lang("$prefix.self");
        foreach ($rawGroups as $group) {
            $protectedGroups[$group->id] = $group->name;
        }
        $group = ORM::factory("group");
        $rawGroups = $group->getItems($filter, $user, 0, null);
        $availableGroups = array();
        foreach ($rawGroups as $group) {
            $availableGroups[$group->id] = $group->name;
        }
        $viewGroups = $protectedGroups + $availableGroups;
        // remove "Public access" for edition
        unset ($protectedGroups[1]);
        $editGroups = $protectedGroups + $availableGroups;
        if (isset($this->accessParent)) {
            $inheritedElement = $this->getAccessParent();
            $data[] = array ('type' => 'check','name' => 'inherit','label' => "$prefix.inherit",'value'=>'1', 'checked'=>$this->inherit);
            if ($this->hasView()){
                $displayInfo = $inheritedElement->getDisplayGroupInfo();
                $data[] = array ('type' => 'display','name' => 'inherit_view','label' => "$prefix.may_view",'value'=>$displayInfo['value'], 'hidden'=>(!$this->inherit));
            }
            if ($this->hasEdit()){
                $editInfo = $inheritedElement->getEditGroupInfo();
                $data[] = array ('type' => 'display','name' => 'inherit_edit','label' => "$prefix.may_edit",'value'=>$editInfo['value'], 'hidden'=>(!$this->inherit));
            }
            /*if ($this->hasContribute()) {
                $contributeInfo = $this->getContributeGroupInfo();
                $data[] = array ('type' => 'display','name' => 'inherit_contribute','label' => "$prefix.may_contribute",'value'=>$contributeInfo['value'], 'hidden'=>(!$this->inherit));
            }*/
        }
        if ($this->hasView()){
            $data[] = array ('type' => 'select','name' => 'view_id','label' => "$prefix.may_view",'required'=>'1', 'values' => $viewGroups, 'value'=>$this->view_id, 'hidden'=>$this->inheritAccess());
        }
        if ($this->hasEdit()){
            $data[] = array ('type' => 'select','name' => 'edit_id','label' => "$prefix.may_edit",'required'=>'1', 'values' => $editGroups, 'value'=>$this->edit_id, 'hidden'=>$this->inheritAccess());
        }
        if ($this->hasContribute()) {
            $data[] = array ('type' => 'separator');
            $data[] = array ('type' => 'select','name' => 'contribute_id','label' => "$prefix.may_contribute",'required'=>'1', 'values' => $editGroups, 'value'=>$this->contribute_id);
        }
    }

    protected function addDisplayableGroups(& $data, $prefix) {
        if ($this->hasView()) {
            $data[] = array_merge(array('type' => 'link', 'label' => "$prefix.may_view"), $this->getDisplayGroupInfo());
        }
        if ($this->hasEdit()) {
            $data[] = array_merge(array('type' => 'link', 'label' => "$prefix.may_edit"), $this->getEditGroupInfo());
        }
        if ($this->hasContribute()) {
            $data[] = array ('type' => 'separator');
            $data[] = array_merge(array('type' => 'link', 'label' => "$prefix.may_contribute"), $this->getContributeGroupInfo());
        }
    }

    public function buildVisibleQuery(& $user, & $query, $tableName = null) {
        // Add current table name
        // ----------------------
        $query['from'][] = $this->table_name;
        
        // If child tableName provided, insert link with child
        // ---------------------------------------------------
        if (isset($tableName)) {
            if (isset($this->actual_object_name))
                $query['where'][] = "$tableName.".$this->actual_object_name."_id = $this->table_name.id";
            else
                $query['where'][] = "$tableName.".$this->object_name."_id = $this->table_name.id";
        }

        
        // Look for items
        // --------------
        // If accessParent is defined, insert parent query to check that parent is visible and look for inherited items
        $inheritCondition = null;
        if (isset ($this->accessParent)) {
            // get parent query
            $queryParent = array('from'=>array(), 'where'=>array());
            $this->getAccessParent()->buildVisibleQuery($user, $queryParent, $this->table_name);
            // 1st - add parent query to current query, to make sure that parent is visible
            $query['from'] = array_merge($query['from'], $queryParent['from']);
            $query['where'] = array_merge($query['where'], $queryParent['where']);
            // 2nd - add parent query to current query with condition on inherit field
            $inheritCondition = "$this->table_name.inherit = 1 and ((".$queryParent['where'][0].") ";
            for ($i=1; $i<count($queryParent['where']); $i++) {
                $inheritCondition.="and (".$queryParent['where'][$i].") ";
            }
            $inheritCondition.=")";
            $ownerField = "access_owner_id";
        } else {
            $ownerField = "owner_id";
        }
        
        // Define special groups
        $ownerGroup = Group_Model::SPECIAL_GROUP_OWNER;
        $publicGroup = Group_Model::SPECIAL_GROUP_PUBLIC;
        $registeredGroup = Group_Model::SPECIAL_GROUP_REGISTERED;
        // If user is set, check that user meets the requirements
        if (isset($user)) {
            $selectionQuery = "$this->table_name.view_id=$publicGroup or $this->table_name.view_id=$registeredGroup or
                               $this->table_name.$ownerField=$user->id or
                                 ($this->table_name.view_id = groups.id and groups_users.group_id= groups.id and groups_users.user_id=$user->id )";
             if ($this->hasEdit())
                $selectionQuery .= " or $this->table_name.edit_id=$registeredGroup  or
                                 ($this->table_name.edit_id = groups.id and groups_users.group_id= groups.id and groups_users.user_id=$user->id )";
             if ($this->hasContribute())
                $selectionQuery .= " or $this->table_name.contribute_id=$publicGroup or $this->table_name.contribute_id=$registeredGroup  or
                                 ($this->table_name.contribute_id = groups.id and groups_users.group_id= groups.id and groups_users.user_id=$user->id )";
        } else {
            // If no user set, check that public access is granted
            $selectionQuery = "$this->table_name.view_id=$publicGroup";
             if ($this->hasContribute())
                 $selectionQuery.= " or $this->table_name.contribute_id=$publicGroup";
        }
        if (isset($inheritCondition))
            $query['where'][] = "($selectionQuery) or ($inheritCondition)";
        else
            $query['where'][] = $selectionQuery;
    }

    protected function buildVisibleItemsQuery(& $filter , & $user, $constraints = null) {
        if (isset($user)) {
            $query = array('from'=>array('groups', 'users', 'groups_users'), 'where'=>array());
        } else {
            $query = array('from'=>array(), 'where'=>array());
        }
        $this->buildVisibleQuery($user, $query);

        // Build SQL query
        $sqlQuery = "select distinct $this->table_name.* from ".$query['from'][0];

        for ($i=1; $i<count($query['from']); $i++) {
            $sqlQuery.=",".$query['from'][$i];
        }
        $sqlQuery.= " where (".$query['where'][0].") ";
        for ($i=1; $i<count($query['where']); $i++) {
            $sqlQuery.="and (".$query['where'][$i].") ";
        }

        // Add constraints
        if (isset($constraints)) {
            foreach ($constraints as $constraint => $value)
                $sqlQuery.="and ($this->table_name.$constraint='$value') ";
        }

        // Add filters
        if (isset($filter)) {
            if ($query2 = $filter->getSQLWhere($this->table_name)) {
                $sqlQuery .= "and $query2";
            }
            $sqlQuery.=$filter->getSQLOrder($this->table_name);
        }

        return $sqlQuery;
    }

    protected function countVisibleItems(& $filter , & $user, $constraints = null) {
        if (isset($user)&&$user->isAdmin()) {
            // Add constraints
            if (isset ($constraints)) {
                foreach ($constraints as $constraint => $value)
                    $this->where($constraint, $value);
            }
            if (isset($filter))
                $filter->add($this);
            return $this->count_all();
        } else {
            $query = $this->buildVisibleItemsQuery($filter , $user, $constraints);
            $result = $this->db->query($query);
            return $result->count();
        }
    }

    protected function getVisibleItems($filter , $user, $offset = 0, $number = null, $constraints = null) {
        if (isset($user)&&$user->isAdmin()) {
            // Add constraints
            if (isset ($constraints)) {
                foreach ($constraints as $constraint => $value)
                    $this->where($constraint, $value);
            }
            if (isset($filter))
                $filter->add($this);
            else
                $this->orderby('name', 'ASC'); // by default, order by name

            if (isset($number))
                $this->limit($number, $offset);

            return $this->find_all();
        } else {
            $query = $this->buildVisibleItemsQuery($filter , $user, $constraints);

            if (isset($number))
                $query.=" limit $offset, $number";
            
            $result = $this->db->query($query);
            if ($result->count()>0)
                return new ORM_Iterator($this,$result);
            else
                return array(); // empty array
        }
    }

    public function inheritAccess() {
        return (isset($this->accessParent)&&($this->inherit==1));
    }

    public function updateAccessControl() {
        $modified = false;
        if (isset($this->accessParent)&&$this->hasOwner()) {
            $this->access_owner_id = $this->getAccessOwnerId();
            $modified = true;
        }
        if ($this->inheritAccess()) {
            if ($this->hasView()) {
                $this->view_id = $this->getDisplayGroupId();
                $modified = true;
            }
            if ($this->hasEdit()) {
                $this->edit_id = $this->getEditGroupId();
                $modified = true;
            }
        }
        if ($modified) {
            parent::save();
        }
        if (isset($this->accessHeirs)){
            foreach ($this->accessHeirs as $heirGroup) {
                $elements = $this->$heirGroup;
                foreach ($elements as $heir) {
                    $heir->updateAccessControl();
                }
            }
        }
    }

    protected function getAccessParent() {
        $parentName = $this->accessParent;
        return $this->$parentName;
    }

    public function save() {
        parent::save();
        $this->updateAccessControl();
    }

}
?>