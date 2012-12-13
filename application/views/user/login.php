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
?>
<script type="text/javascript">
    function login() {
        var element = $('user_login');
        if (element.visible())
            element.hide();
        else {
            Effect.Appear(element, {duration:0.5});
            window.setTimeout("$('user').focus()",1000);
        }
    }
</script>
<ul class='menu_content'>
    <li><a href="javascript:login()" title="<?php echo Kohana::lang('user.connect');?>"><?php echo html::image(array("src"=>Kohana::config("toucan.images_directory")."/connect.png"));?></a></li>
</ul>
<div id='user_login'
<?php
	if (isset($log_error)&&(strlen($log_error)>0)){
        echo "> <span class='error'>$log_error</span>";
	} else {
        echo "style='display:none'>";
    }
    echo form::open();
    echo form::label('user', Kohana::lang('user.username'));
    echo form::input('user', $this->input->post('user'),"style='width:100px'");
    echo form::label('password', Kohana::lang('user.password'));
    echo form::password('password', '','style="width:100px"');
    echo "<br/>";
    echo form::submit('submit', Kohana::lang('user.connect'), "class = 'toucan_button'");
    if (isset($url)) {
        echo form::hidden('url', html::url($url));
    }
    echo form::close();

    echo "<br/>";
    echo html::anchor('user/sendPassword',Kohana::lang('user.forget'));
    echo "<br/>";
    if (isset($may_register)&&$may_register)
        echo html::anchor('user/register',Kohana::lang('user.register'));

?>
</div>
<?php
	if (isset($log_error)&&(strlen($log_error)>0)){
?>
    <script type='text/javascript'>
        document.observe("dom:loaded", function() {
            $("user").focus();
        });
    </script>
<?php
}
?>