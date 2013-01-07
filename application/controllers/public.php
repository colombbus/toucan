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

class Public_Controller extends DataPage_Controller {

    protected $context = array();
    protected $sessionName = "formSession";
    protected $copyName = "formCopy";
    
    public function __construct() {
        parent::__construct();
    }

    protected function loadTemplate(& $session) {
        if ($session->style_id>0) {
            $style = $session->style;
            $this->template = new TemplateView($style->getViewFile());
            $this->template->setStyle($style);
            $this->template->title = 'Toucan';
        } else {
            $this->template = new TemplateView(Style_Model::getDefaultViewFile());
            $this->template->title = 'Toucan';
        }
    }

    protected function setHeaders($action) {

    }

    protected function setActions($action) {
        $actions = array();
        switch ($action) {
            case 'CREATE' :
                $actions[] = array('type'=>'submit', 'text'=>'button.send');
                break;
            case 'PASSWORD' :
                $actions[] = array('type'=>'submit', 'text'=>'button.send');
                break;
        }
        $this->template->content->actions = $actions;
    }

    protected function controlAccess($action) {
        switch($action) {
            case 'CREATE':
            case 'PASSWORD':
                break;
            default :
                $this->displayError('restricted_access');
                break;
        }
    }

    protected function setPath($action) {

    }

    protected function setDescription($action) {
    }

    public function form($id) {
        $formSession = ORM::factory($this->sessionName, $id);
        if (!isset($formSession)||!$formSession->loaded) {
            $this->displayError('session_unknown');
        }
        if (!$formSession->mayBeEditedByPublic()) {
            $this->displayError('private_session');
        }
        if (!$formSession->isOpen()) {
            $this->displayError('session_closed');
        }
        if (!$formSession->isParentOpen()) {
            $this->displayError('evaluation_closed');
        }
        $this->context['sessionId'] = $id;

        // Set form language 
        if (isset($formSession->language))
            language::setCurrentLanguage($formSession->language);
        
        $this->loadTemplate($formSession);
        $this->template->title = $formSession->name;
        $description = $formSession->description;
        
        if (strlen(trim($description))>0) {
            $this->template->description = str_replace("\n", "<br/>", htmlspecialchars($description,ENT_QUOTES, "UTF-8"));
        } else {
            $this->template->description = Kohana::lang('public.form_description');
        }

        $this->template->setStep("form");

        $errors = array();
        $password = null;
        if (($post = $this->input->post())&&isset($post["form_public_password"])) {
            if (!isset($post['form_password'])) {
                $this->displayError('error');
            }
            if (($formSession->password_flag==0)||($formSession->password == $post['form_password'])) {
                // test ok
                $password = $post['form_password'];
            } else {
                // wrong password
                $errors['form_password'] = Kohana::lang('form_errors.wrong_password');
            }
        }
        if (($post = $this->input->post())&&isset($post['form_create_'.$this->copyName])) {
            if (isset($post["form_password"])) {
                if (($formSession->password_flag==0)||($formSession->password == $post['form_password'])) {
                    // test ok
                    $password = $post['form_password'];
                }
            }
        }
        if (($formSession->password_flag == 1)&&(!isset($password))) {
            // protected session
            $passData = array();
            $passData[] = array('type'=>'password', 'name'=>'form_password', 'label'=>'public.password');
            $this->template->content=new View('data/edit');
            $this->template->content->formId = "form_public_password";
            $this->template->content->data = $passData;
            $this->template->content->errors = $errors;
            $this->template->message = Kohana::lang('public.password_description');
            $this->template->setStep("password");
            $this->setPageInfo('PASSWORD');
        } else {
            $this->createForm($id, $password);
			$item = ORM::factory($this->copyName);
            $this->template->content->questionConditionals = $item->getConditionals();
            if ($item->getRequiredState())
                $this->template->content->requiredText = Kohana::lang($this->copyName.'.required_text');
		}
    }

    protected function createForm($id, $password = null) {
        $this->context['sessionId'] = $id;
        $this->dataName = $this->copyName;
        $parameters = array('session_id'=>$id,'url_next'=>"public/end/$id");
        if (isset($password)) {
            $parameters['form_password'] = $password;
        }
        $this->create($parameters,'copy/edit');
        $this->template->content->goingOnState = CopyState_Model::GOING_ON;
    }

    public function end($id) {
        $formSession = ORM::factory($this->sessionName, $id);
        $this->loadTemplate($formSession);
        $this->template->content= "";
        $this->template->title = $formSession->name;
        $description = $formSession->description;

        if (strlen(trim($description))>0) {
            $this->template->description = $description;
        } else {
            $this->template->description = Kohana::lang('public.form_description');
        }
        $this->template->message = Kohana::lang('public.save_successful');
        $this->template->setStep("end");
    }

}
?>