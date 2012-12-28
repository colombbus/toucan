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

class User_Model extends Auth_User_Model {

    protected $has_and_belongs_to_many = array('roles','groups');
    protected $has_one = array('logo'=>'file');
    protected $validation = null;
    protected $ignored_columns = array('password_confirm','admin','active','photo','fullName','email_option', 'innerOptions', 'delete_photo');
    protected $innerOptions = array();
    protected $birthdayValue = "";
    
    const PROTECTED_ID = 1;

    public function __construct($id = NULL) {
        parent::__construct($id);
    }

    public function getDisplayableData($access, & $user = null) {
        $displayableData = array();
        $admin = ($access==access::ADMIN);
        $showHidden = ($admin)|($access == access::OWNER);

        // FIRST NAME AND NAME
        $displayableData[] = array ('type' => 'text', 'label' => 'user.firstname', 'value'=> $this->firstname);
        $displayableData[] = array ('type' => 'text', 'label' => 'user.name', 'value'=> $this->name);
        $displayableData[] = array ('type' => 'text', 'label' => 'user.sex', 'value'=> Kohana::lang('user.sex'.$this->sex));
        // USERNAME & EMAIL
        $displayableData[] = array ('type' => 'separator');
        $displayableData[] = array ('type' => 'text', 'label' => 'user.username', 'value'=> $this->username);
        if ((isset($this->email))&&($showHidden||$this->getOption('email'))) {
            $displayableData[] = array ('type' => 'text', 'label' => 'user.email', 'value'=> $this->email);
        }
        // GENERAL INFO
        $displayableData[] = array ('type' => 'separator');
        $displayableData[] = array ('type' => 'text', 'label' => 'user.location', 'value'=> $this->location);
        $displayableData[] = array ('type' => 'text', 'label' => 'user.birthday', 'value'=> $this->birthdayValue);
        if (isset($this->extra)) {
            $displayableData[] = array ('type' => 'long_text', 'label' => 'user.extra', 'value'=> $this->extra);
        }
        // CONNEXIONS, ADMIN & ACTIVE
        if ($admin) {
            $displayableData[] = array ('type' => 'separator');
            $displayableData[] = array ('type' => 'text', 'label' => 'user.creation_date', 'value'=> Utils::translateTimestamp($this->created));
            $displayableData[] = array ('type' => 'text', 'label' => 'user.connections', 'value'=> $this->logins);
            if ($this->last_login>0)
                $displayableData[] = array ('type' => 'text', 'label' => 'user.last_login', 'value'=> Utils::translateTimestamp($this->last_login));
            if ($this->isAdmin())
                $value = Kohana::lang('user.yes');
            else
                $value = Kohana::lang('user.no');
            $displayableData[] = array ('type' => 'text', 'label' => 'user.admin', 'value'=> $value);
            if ($this->isActive()) {
                $value = Kohana::lang('user.yes');
            } else {
                $value = Kohana::lang('user.no');
            }
            $displayableData[] = array ('type' => 'text', 'label' => 'user.active', 'value'=> $value);
        }

        return $displayableData;
    }

    public function getEditableData($access, & $user) {
        $admin = ($access==access::ADMIN);
        $editableData = array();

        $editableData[] = array ('type' => 'text','name' => 'firstname','label' => 'user.firstname','required'=>'1', 'value' => $this->firstname);
        $editableData[] = array ('type' => 'text','name' => 'name','label' => 'user.name','required'=>'1', 'value' => $this->name);
        $editableData[] = array ('type' => 'choice','name' => 'sex','label' => 'user.sex','values'=>array(array('value'=>'0', 'label'=>Kohana::lang('user.sex0')),array('value'=>'1', 'label'=>Kohana::lang('user.sex1'))), 'value' => $this->sex);
        $editableData[] = array ('type' => 'separator');
        $editableData[] = array ('type' => 'text','name' => 'username','label' => 'user.username','required'=>'1', 'value' => $this->username);
        $editableData[] = array ('type' => 'text','name' => 'email','label' => 'user.email','required'=>'1', 'value' => $this->email);
        $editableData[] = array ('type' => 'check','name' => 'email_option','label' => 'user.access_email','checked' => $this->getOption('email'));
        $editableData[] = array ('type' => 'separator');
        $editableData[] = array ('type' => 'text','name' => 'location', 'label' => 'user.location', 'value' => $this->location);
        $editableData[] = array ('type' => 'date','name' => 'birthday', 'label' => 'user.birthday', 'value' => $this->birthdayValue);
        $editableData[] = array ('type' => 'long_text','name' => 'extra', 'label' => 'user.extra', 'value' => $this->extra);
        $editableData[] = array ('type' => 'separator');
        if (isset($this->logo_id) && $this->logo_id>0) {
            $editableData[] = array ('type' => 'file','name' => 'photo', 'label' => 'user.modify_photo', 'value'=>$this->logo->path);
            $editableData[] = array ('type' => 'check','name' => 'delete_photo', 'label' => 'user.delete_photo');
        } else {
            $editableData[] = array ('type' => 'file','name' => 'photo', 'label' => 'user.add_photo');
        }
        $editableData[] = array ('type' => 'separator');
        if ($admin&&!$this->isProtected()) {
            $editableData[] = array ('type' => 'check','name' => 'active', 'label' => 'user.active', 'checked' => $this->isActive());
            $editableData[] = array ('type' => 'check','name' => 'admin', 'label' => 'user.admin', 'checked' => $this->isAdmin());
        } else {
            $editableData[] = array ('type' => 'hidden','name' => 'active', 'value' => $this->isActive());
        }

        return $editableData;
    }

    public function getCreationData($access, & $user, & $parameters = null) {
        $admin = ($access==access::ADMIN);

        $registrationData = array();

        $registrationData[] = array ('type' => 'text','name' => 'firstname','label' => 'user.firstname','required'=>'1', 'value' => $this->firstname);
        $registrationData[] = array ('type' => 'text','name' => 'name','label' => 'user.name','required'=>'1', 'value' => $this->name);
        $registrationData[] = array ('type' => 'choice','name' => 'sex','label' => 'user.sex','values'=>array(array('value'=>'0', 'label'=>Kohana::lang('user.sex0')),array('value'=>'1', 'label'=>Kohana::lang('user.sex1'))), 'value' => $this->sex, 'required'=>'1');
        $registrationData[] = array ('type' => 'separator');
        $registrationData[] = array ('type' => 'text','name' => 'username','label' => 'user.username','required'=>'1', 'value' => $this->username);
        $registrationData[] = array ('type' => 'password','name' => 'password','label' => 'user.password','required'=>'1');
        $registrationData[] = array ('type' => 'password','name' => 'password_confirm','label' => 'user.password2','required'=>'1');
        $registrationData[] = array ('type' => 'text','name' => 'email','label' => 'user.email','required'=>'1', 'value' => $this->email);
        $registrationData[] = array ('type' => 'check','name' => 'email_option','label' => 'user.access_email','checked' => $this->getOption('email'));
        $registrationData[] = array ('type' => 'separator');
        $registrationData[] = array ('type' => 'text','name' => 'location', 'label' => 'user.location', 'value' => $this->location);
        $registrationData[] = array ('type' => 'date','name' => 'birthday', 'label' => 'user.birthday', 'value' => $this->birthdayValue);
        $registrationData[] = array ('type' => 'long_text','name' => 'extra', 'label' => 'user.extra', 'value' => $this->extra);
        $registrationData[] = array ('type' => 'separator');
        $registrationData[] = array ('type' => 'file','name' => 'photo', 'label' => 'user.add_photo');
        if ($admin) {
            $registrationData[] = array ('type' => 'separator');
            $registrationData[] = array ('type' => 'check','name' => 'active', 'label' => 'user.active', 'checked' => $this->isActive());
            $registrationData[] = array ('type' => 'check','name' => 'admin', 'label' => 'user.admin', 'checked' => $this->isAdmin());
        } else {
            $registrationData[] = array ('type' => 'hidden','name' => 'active', 'value' => $this->isActive());
        }
        return $registrationData;
    }

    public function getPasswordEditableData() {
        $editableData = array();
        $editableData[] = array ('type' => 'password','name' => 'password','label' => 'user.password','required'=>'1');
        $editableData[] = array ('type' => 'password','name' => 'password_confirm','label' => 'user.password2','required'=>'1');
        return $editableData;
    }

    public function getGroupsEditableData(& $user) {
        $editableData = array();
        $filter = Filter::instance();
        $filter->setSorting("name");
        $groups = ORM::factory('group')->getItems($filter, $user);
        $userGroups = $this->groups->primary_key_array();
        foreach ($groups as $group) {
            $editableData[] = array ('type' => 'check','name' => 'group[]', 'value' => $group->id, 'translated_label' => $group->name, 'checked' => in_array($group->id,$userGroups));
        }
        return $editableData;
    }

    public function validateEdition(array & $array, & $user, $save = false) {
        // 1st handle admin and active flags
        if ($this->isProtected()) {
            $admin = true;
            $active = true;
        } else {
            $admin = isset($array['admin']);
            $active = isset($array['active']);
        }

 
        // Initialise the validation library and setup some rules
        $this->validation = Validation::factory($array);
        $this->buildEditValidation();

        if ($result =  ORM::validate($this->validation, false)) { // 'ORM' instead of 'parent' to bypass auth_user validation

            // Validate optional uploaded files
            $this->validation = Validation::factory($_FILES);
            $this->buildAvatarValidation();

            if ($result =  ORM::validate($this->validation, false)) {
                $this->setOptions($array);
                $this->setAdmin($admin, false);
                $this->setActive($active, false);
                if (isset($array['delete_photo'])&& $array['delete_photo'] == 1 && $this->logo_id>0) {
                    $this->logo->delete();
                    $this->logo_id = 0;
                }
                if (upload::required($_FILES['photo']))
                    $this->setAvatarFromUploadedPhoto(false);
                if ($save)
                    $this->save();
            }
        }
        return $result;
    }

    public function validatePassword(array & $array, $save = false) {
        // Initialise the validation library and setup some rules
        $this->validation = Validation::factory($array);
        $this->buildPasswordValidation();

        // 'ORM' instead of 'parent' to bypass auth_user validation
        if ($result =  ORM::validate($this->validation)) {
            $this->password = $array['password'];
            if ($save)
                $this->save();
        }
        return $result;
    }


    public function validateCreation(array & $array,& $user, $save = false) {
        // 1st handle admin and active flags
        $admin = isset($array['admin']);
        $active = isset($array['active']);
        $pending = isset($array['pending_url']);

        // Initialise the validation library and setup some rules
        $this->validation = Validation::factory($array);
        $this->buildPasswordValidation();
        $this->buildEditValidation();

        if ($result =  ORM::validate($this->validation, false)) { // 'ORM' instead of 'parent' to bypass auth_user validation
            // Validate optional uploaded files
            $this->validation = Validation::factory($_FILES);
            $this->buildAvatarValidation();

            if ($result =  ORM::validate($this->validation, false)) {
                $this->setOptions($array);
                $this->setAdmin($admin, false);
                $this->setActive($active, false);
                $this->setPending($pending, false);
                if (upload::required($_FILES['photo']))
                    $this->setAvatarFromUploadedPhoto(false);
                $this->created = time();
                if ($save)
                    $this->save();
                if ($pending) {
                    // Send the email for account validation
                    $this->sendValidationEmail($array['pending_url']);
                }
            }
        }
        return $result;
    }

    public function validateGroupSelection(& $array, $save = false) {
        // 1st Validation of group Ids
        if ((!isset($array['group']))||(!is_array($array['group']))) {
            $ids = array();
        } else {
            $ids = $array['group'];
            $groupsNumber = ORM::factory('group')->in('id', $ids)->count_all();
            if ($groupsNumber != count($ids)) {
                $this->validation = Validation::factory($array);
                $this->validation->add_error('main','groups_wrong_id');
                return false;
            }
        }

        // 2nd set groups
        $this->groups = $ids;
        $this->computeGroups();
        if ($save)
            $this->save();
        return true;
    }

    private function buildEditValidation() {
        $this->validation->pre_filter('trim')
        ->add_rules('username', 'required', 'length[4,32]',array($this, 'correctUsername'), array($this, 'uniqueUsername'))
        ->add_rules('email', 'required', 'valid::email', /*'valid::email_domain',*/'length[1,127]',array($this, 'uniqueEmail'))
        ->add_rules('name', 'required', 'length[1,127]')
        ->add_rules('firstname', 'required', 'length[1,127]')
        ->add_rules('location','length[1,127]')
        ->add_rules('extra','length[0,500]')
        ->add_rules('email_option','in_array[0,1]')
        ->add_rules('extra_option','in_array[0,1]')
        ->add_rules('photo_option','in_array[0,1]')
        ->add_callbacks('birthday',array($this, 'checkValidDate'))
        ->add_rules('sex','required', 'in_array[0,1]');
    }

    private function buildPasswordValidation() {
        $this->validation->pre_filter('trim')
        ->add_rules('password', 'required', 'length[5,20]')
        ->add_rules('password_confirm', 'required', 'matches[password]', 'length[5,20]');
    }

    private function buildAvatarValidation() {
        $this->validation->add_rules('photo', 'upload::valid', 'upload::type[gif,jpg,png,jpeg]', 'upload::size[1M]');
    }


    public function getErrors($lang_file=null) {
        if ($this->validation === null) {
            return null;
        } else {
            if ($lang_file!==null) {
                return $this->validation->errors($lang_file);
            } else {
                return $this->validation->errors();
            }
        }
    }

    public function correctUsername($username) {
        return (bool) preg_match('/^[-\pL\pN_]++$/uD', (string) $username);
    }

    public function uniqueUsername($username) {
        $other = $this->db->query("SELECT id from users WHERE id != $this->id AND username = '$username'");
        return !($other->count() > 0);
    }

    public function uniqueEmail($email) {
        $other = $this->db->query("SELECT id from users WHERE id != $this->id AND email = '$email'");
        return !($other->count() > 0);
    }

    public function checkValidDate(Validation $valid) {
        if (array_key_exists('birthday', $valid->errors()))
            return;
        if (strlen($valid->birthday)==0) {
            $valid->birthday = NULL;
            return;
        }
        $birthday = Utils::date_str2db($valid->birthday,Kohana::lang("calendar.format"));
        if ($birthday === FALSE)
            $valid->add_error('birthday', 'valid_date');
        else
            $valid->birthday = $birthday;
    }

    public function setValues(& $array) {
        $this->load_values($array);
        $this->setOptions($array);
        if (isset($array['birthday'])) {
            $this->birthdayValue = $array['birthday'];
        }
    }

    public function load_values(array $values) {
        parent::load_values($values);
        $this->object['fullName'] = $this->firstname." ".$this->name;
        $this->innerOptions = unserialize($this->options);
        if ($this->innerOptions === FALSE) 
            $this->innerOptions = array();
        $this->birthdayValue = Utils::translateDate($this->birthday);
        return $this;
    }

    public function isAdmin() {
        return ($this->has(ORM::factory('role', Role_Model::ADMIN)));
    }

    public function setAdmin($value,$save = false) {
        if ($value != $this->isAdmin()) {
            if (!$value) {
                $this->remove(ORM::factory('role', Role_Model::ADMIN));
            } else {
                $this->add(ORM::factory('role', Role_Model::ADMIN));
            }
            if ($save)
                $this->save();
        }
    }

    public function isActive() {
        if (!$this->loaded) {
            // TODO: vérifier ce point
            return true; // By default, a new user is set to active
        }
        return ($this->has(ORM::factory('role', Role_Model::LOGIN)));
    }

    public function isPending() {
        if (!$this->loaded) {
            return false;
        }
        return ($this->has(ORM::factory('role', Role_Model::PENDING)));
    }

    public function setActive($value, $save = false) {
        if (!($this->loaded)||($value != $this->isActive())) {
            if (!$value)
                $this->remove(ORM::factory('role', Role_Model::LOGIN));
            else {
                $this->add(ORM::factory('role', Role_Model::LOGIN));
                $this->setPending(false, false);
            }
            if ($save)
                $this->save();
        }
    }

    public function setPending($value, $save = false) {
        if (!($this->loaded)||($value != $this->isPending())) {
            if (!$value)
                $this->remove(ORM::factory('role', Role_Model::PENDING));
            else {
                $this->add(ORM::factory('role', Role_Model::PENDING));
                $this->setActive(false, false);
            }
            if ($save)
                $this->save();
        }
    }


    public function setAvatarFromUploadedPhoto($save = false) {
        switch ($_FILES['photo']['type']) {
            case "image/jpeg":
                $extension=".jpg";
                break;
            case "image/gif":
                $extension=".gif";
                break;
            case "image/png":
                $extension=".png";
                break;
        }
        $file = File_Model::newFile('avatar',$extension);

        $filename = upload::save('photo');

        $image = Image::factory($filename);
        if ($image->width>150 || $image->height>70)
            $image->resize(150, 70, Image::AUTO);
        $image->save($file->getFile(),Kohana::config('toucan.public_file_mode'));

        unlink($filename);

        $this->logo_id = $file->id;

        if ($save)
            $this->save();
    }

    public function delete() {
        if ($this->loaded) {
            // 1st: remove the user from every group
            $groups = $this->groups;
            foreach ($groups as $group) {
                $this->remove($group);
            }

            // 2nd: remove every group belonging to this user
            $ownGroups = ORM::factory('group')->where('owner_id', $this->id)->find_all();
            foreach ($ownGroups as $group) {
                $group->delete();
            }

            // 3rd: for every elements belonging to this user, put administrator as owner
            $administrator = ORM::factory('user')->find(self::PROTECTED_ID);
            $activities = ORM::factory('activity')->where('owner_id', $this->id)->find_all();
            foreach ($activities as $activity) {
                $activity->setOwner($administrator, true);
            }
            $evaluations = ORM::factory('evaluation')->where('owner_id', $this->id)->find_all();
            foreach ($evaluations as $evaluation) {
                $evaluation->setOwner($administrator, true);
            }
            $sessions = ORM::factory('formSession')->where('owner_id', $this->id)->find_all();
            foreach ($sessions as $session) {
                $session->setOwner($administrator, true);
            }
            $sessions = ORM::factory('interviewSession')->where('owner_id', $this->id)->find_all();
            foreach ($sessions as $session) {
                $session->setOwner($administrator, true);
            }
            $copies = ORM::factory('formCopy')->where('owner_id', $this->id)->find_all();
            foreach ($copies as $copy) {
                $copy->setOwner($administrator, true);
            }
            $copies = ORM::factory('interviewCopy')->where('owner_id', $this->id)->find_all();
            foreach ($copies as $copy) {
                $copy->setOwner($administrator, true);
            }
            $indicators = ORM::factory('indicator')->where('owner_id', $this->id)->find_all();
            foreach ($indicators as $indicator) {
                $indicator->setOwner($administrator, true);
            }
            $templates = ORM::factory('formTemplate')->where('owner_id', $this->id)->find_all();
            foreach ($templates as $template) {
                $template->setOwner($administrator, true);
            }
            $templates = ORM::factory('interviewTemplate')->where('owner_id', $this->id)->find_all();
            foreach ($templates as $template) {
                $template->setOwner($administrator, true);
            }

            // 4th: delete cached groups
            $query = "delete from `cached_groups` where `user_id`='".$this->id."'";
            $result = $this->db->query($query);
            
            // 5th: delete user
            parent::delete();
        }
    }

    public function memberOf(& $group) {
        if (!$this->loaded)
            return false;
        return ($this->has($group));
    }

    public function owns(& $object) {
        if (!$this->loaded)
            return false;
        if (isset($object)&method_exists($object,"isOwner"))
            return $object->isOwner($this);
        return false;
    }

    public function isOwner(& $user) {
        return ($this->loaded)&&isset($user)&&($user->id == $this->id);
    }

    public function isEditable() {
        return !$this->isProtected();
    }

    public function isEditableBy(& $user) {
        if (isset($user))
            return ($user->id == $this->id);
        return false;
    }

    public function isViewableBy(& $user) {
        return (isset($user));
    }

    public function mayBeContributedBy(& $user) {
        return false;
    }

    public function mayView(& $object) {
        if (!$this->loaded)
            return false;
        if (isset($object)&&method_exists($object,"isViewableBy"))
            return $object->isViewableBy($this);
        return false;
    }

    public function mayContribute(& $object) {
        if (!$this->loaded)
            return false;
        if (isset($object)&&method_exists($object,"mayBeContributedBy"))
            return $object->mayBeContributedBy($this);
        return false;
    }

    public function mayEdit(& $object) {
        if (!$this->loaded)
            return false;
        if (isset($object)&&method_exists($object,"isEditableBy"))
            return $object->isEditableBy($this);
        return false;
    }

    public function isProtected() {
        return ($this->id == User_Model::PROTECTED_ID);
    }

    public function registerGroup(& $group, $value, $save) {
        if (!$this->loaded)
            return false;
        $groups = $this->groups->primary_key_array();
        if ($value) {
            // register
            if (!in_array($group->id, $groups)) {
                $groups[] = $group->id;
                $this->groups = $groups;
                if ($save)
                    $this->save();
            }
        } else {
            // unregister
            $key = array_search( $group->id, $groups);
            if ($key !== FALSE) {
                unset($groups[$key]);
                $this->groups = $groups;
                if ($save)
                    $this->save();
            }
        }
        $this->computeGroups();
        return true;
    }

    public function count(& $filter , & $user, $groupId = null) {
        $activeOnly = !$user->isAdmin();
        if ($activeOnly) {
            if (isset($groupId)) {
                $query = "select users.id from roles_users, users, groups_users, roles where users.id = groups_users.user_id and groups_users.group_id = '$groupId' and users.id = roles_users.user_id and roles_users.role_id = roles.id and roles.name = 'login'";
            } else {
                $query = "select users.id from roles_users, users, roles where users.id = roles_users.user_id and roles_users.role_id = roles.id and roles.name = 'login'";
            }
            // Add Where filter
            if (isset ($filter)) {
                if ($query2 = $filter->getSQLWhere("users")) {
                    $query .= " and $query2";
                }
            }
            $result = $this->db->query($query);
            return $result->count();
        } else {
            if (isset($groupId)) {
                $group = ORM::factory('group',$groupId);
                if (isset($filter))
                    $filter->add($group);
                return $group->users->count();
            } else {
                if (isset($filter))
                    $filter->add($this);
                return $this->count_all();
            }
        }
    }

    public function getItems(& $filter, & $user, $offset = 0, $number = null, $groupId = null) {
        $activeOnly = !$user->isAdmin();
        if ($activeOnly) {
            if (isset($groupId)) {
                $query = "select users.* from roles_users, users, groups_users, roles where users.id = groups_users.user_id and groups_users.group_id = '$groupId' and users.id = roles_users.user_id and roles_users.role_id = roles.id and roles.name = 'login'";
            } else {
                $query = "select users.* from roles_users, users, roles where users.id = roles_users.user_id and roles_users.role_id = roles.id and roles.name = 'login'";
            }
            // Add filters
            if (isset($filter)) {
                if ($query2 = $filter->getSQLWhere("users")) {
                    $query .= "and $query2";
                }
                $query.=$filter->getSQLOrder("users");
            }
            if (isset($number))
                    $query.=" limit $offset, $number";
            $result = $this->db->query($query);
            if ($result->count()>0)
                return new ORM_Iterator($this, $result);
            else
                return null;
        } else {
            if (isset($groupId)) {
                $group = ORM::factory('group',$groupId);
                if (isset ($filter))
                    $filter->add($group);
                if (isset($number))
                    $group->limit($number, $offset);
                return $group->users;
            } else {
                if (isset($filter))
                    $filter->add($this);
                if (isset($number))
                    $this->limit($number, $offset);
                return $this->find_all();
            }
        }
    }

    public function getPasswordRecoveryData() {
        $editableData = array();
        $editableData[] = array ('type' => 'text','name' => 'email','label' => 'user.email','required'=>'1', 'value'=>$this->email);
        return $editableData;
    }

    public function retrieveAndSendPassword($array) {
        $this->validation = Validation::factory($array)
            ->pre_filter('trim')
            ->add_rules('email', 'required', 'valid::email', 'length[1,127]');
        if ($this->validation->validate()) {
            $email = $array['email'];
            $user = ORM::factory('user')->where('email', $email)->find_all();
            if ($user->count()>0) {
                // From, subject and HTML message
                $newPassword = self::generatePassword();
                $from = array('toucan@colombbus.org',Kohana::lang('user.password_message_from'));
                $subject = Kohana::lang('user.password_message_subject');
                $message = sprintf(Kohana::lang('user.password_message_contents'), $newPassword);
                $sent = email::send($email, $from, $subject, $message, true);
                if ($sent>0) {
                    // message sent
                    $user = $user->current();
                    $user->password = $newPassword;
                    $user->save();
                    return true;
                } else {
                    $this->validation->add_error('main', 'email_problem');
                }
            } else {
                $this->validation->add_error('email', 'unknown');
            }
            return false;
        }
        return false;
    }

    public function sendValidationEmail($baseUri) {
        // From, subject and HTML message
        $validationChain = $this->generateValidationChain();
        if (!isset($validationChain))
            return false;
        $validationUrl = sprintf($baseUri, $this->id, $validationChain);
        $from = array('toucan@colombbus.org',Kohana::lang('user.validation_message_from'));
        $subject = Kohana::lang('user.validation_message_subject');
        $message = sprintf(Kohana::lang('user.validation_message_contents'), $validationUrl, $validationUrl);
        $sent = email::send($this->email, $from, $subject, $message, true);
        return ($sent>0);
    }

    public function checkValidation($validationChain) {
        if (strcasecmp($validationChain, $this->generateValidationChain())==0) {
            $this->setPending(false, false);
            $this->setActive(true,true);
            return true;
        }
        return false;
    }

    public function setEmailValue($array) {
        if (isset($array['email'])) {
            $this->email = $array['email'];
        }
    }

    protected static function generatePassword() {
        $password = "";
        $charNumber = rand(6,8);
        for ($i=0; $i<$charNumber; $i++) {
          $d=rand(1,30)%2;
          if ($d)
            $password .= chr(rand(65,90));
          else
            $password .= chr(rand(48,57));
        }
        return $password;
    }

    protected function generateValidationChain() {
        if (!$this->loaded) {
            return null;
        }
        return md5("Toucan validation".$this->password.$this->created);
    }
    
    public function setOption($name, $value, $save = false) {
        $this->innerOptions[$name] = $value;
        $this->changed['options'] = 'options';
        $this->saved = FALSE;
        if ($save)
            $this->save();
    }
    
    public function getOption($name, $default = FALSE) {
        if (isset($this->innerOptions[$name]))
            return $this->innerOptions[$name];
        else
            return $default;
    }
    
    protected function setOptions(& $array) {
        if (isset($array['email_option'])&&$array['email_option']==1)
            $this->setOption('email', true);
        else
            $this->setOption('email', false);
    }

    public function save() {
        $this->options = serialize($this->innerOptions);
        parent::save();
    }
    
    public function getGroups() {
        $query = "select `groups` from `cached_groups` where `user_id`='".$this->id."'";
        $result = $this->db->query($query);
        if ($result->count()>0) {
            return $result->current()->groups;
        } else {
            return $this->computeGroups();
        }
    }
    
    protected function computeGroups() {
        $groups = $this->groups->primary_key_array();
        $cachedGroups = implode(',',$groups);
        $query = "insert into `cached_groups` (`user_id`, `groups`) values ('$this->id', '$cachedGroups') on duplicate key update `groups`='$cachedGroups'";
        $result = $this->db->query($query);
        return $cachedGroups;
    }

    public function __set($column, $value) {
        if ($column == 'logins') {
            // user has logged in: update cached groups
            $this->computeGroups();
        }
        parent::__set($column, $value);
    }
}
?>