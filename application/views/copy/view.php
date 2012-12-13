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
<?php
    if (isset($joinFileUrl)) {
?>
<script type = "text/javascript">
    function joinFile() {
        $('file_container_new').show();
    }

    function cancelJoinFile() {
        $('file_container_new').hide();
    }

    function deleteFile(id) {
        elementName = "file_container_"+id;
        if (window.confirm("<?php echo addslashes(Kohana::lang($confirmFileDeletion)); ?>")) {
            new Ajax.Request('<?php echo html::url($deleteFileUrl)?>/'+id, { method: 'get'});
            $(elementName).remove();
        }
    }

    function editFile(id) {
        elementName = "file_container_"+id;
        new Ajax.Updater(elementName, '<?php echo html::url($editFileUrl)?>/'+id, {
            method: 'get',
            evalScripts: false
        });

    }

    function saveFile(id) {
        elementName = "file_container_"+id;
        formName = "form_edit_file_"+id;
        new Ajax.Updater(elementName, '<?php echo html::url($editFileUrl)?>/'+id , {
            method: 'post',
            parameters:$(formName).serialize(true),
            evalScripts: false
        });

    }

    function cancelEditFile(id) {
        elementName = "file_container_"+id;
        new Ajax.Updater(elementName, '<?php echo html::url($showFileUrl)?>/'+id, {
            method: 'get',
            evalScripts: false
        });

    }


</script>
<?php
    }
?>
<?php
    include APPPATH."/views/data/inc_description.php";
    echo "<div class = 'copy_navigation'>";
    if (isset($previous)) {
        echo "<a class = 'previous' href=\"".html::url($previous['link'])."\">".Kohana::lang($previous['label'])."</a>";
    }
    if (isset($next)) {
        echo "<a class = 'next' href=\"".html::url($next['link'])."\">".Kohana::lang($next['label'])."</a>";
    }
    //echo "<div class='float_end'></div>";
    echo "</div>";
    include APPPATH."/views/data/inc_display.php";
    echo "<table class='toucan_display view_copy'>";
    foreach ($data as $item) {
        displayItem($item);
    }
    echo "</table>";
    if (isset($joinFileUrl)) {
        echo "<ul id = 'files'>";
        if (isset($filesData)) {
            foreach($filesData as $fileData) {
                echo "<li class = 'file' id='file_container_".$fileData['id']."'>";
                    foreach ($fileData['data'] as $fileItem)
                        displayItem($fileItem, false);
                    if ($mayEditFiles) {
                        echo "<div class = 'file_edit_actions'>";
                        echo "<a href='javascript:editFile(".$fileData['id'].")'>".Kohana::lang($editFile)."</a>";
                        echo "<a href='javascript:deleteFile(".$fileData['id'].")'>".Kohana::lang($deleteFile)."</a>";
                        echo "</div>";
                    }
                echo "</li>";
            }
        }
        echo"</ul>";
        // Form to create a new file
        include APPPATH."/views/data/inc_edit.php";
        if (isset($formErrors)) {
            echo "<div class='form' id='file_container_new'>\n";
        } else {
            $formErrors = array();
            echo "<div class='form' id='file_container_new' style='display:none;'>\n";
        }
        echo form::open_multipart(html::url($joinFileUrl), array('id'=>'form_join_file'));
        echo form::hidden('form_join_file','1');
        echo "<table class='toucan_edition'>";
        foreach ($newFileData as $item) {
            displayEditItem($item, $formErrors, true);
        }
        echo "<tr><td class='edition_label'></td><td class='edition_entry'>";
        echo "<div class=\"file_actions\">";
        echo form::button(array ('type'=>'button', 'name'=>"button_cancel_file"), Kohana::lang($cancelFile),"onClick=\"cancelJoinFile()\" class=\"toucan_button\"");
        echo form::button(array ('type'=>'submit', 'name'=>"button_submit_file"), Kohana::lang($submitFile), "class=\"toucan_button\" class = \"toucan_button\"");
        echo "</div>";
        echo "</td></tr>";
        echo "</table>";
        echo form::close();
        echo "</div>\n";

    }
    include APPPATH."/views/data/inc_information.php";
    echo "<div class = 'copy_navigation'>";
    if (isset($previous)) {
        echo "<a class = 'previous' href=\"".html::url($previous['link'])."\">".Kohana::lang($previous['label'])."</a>";
    }
    if (isset($next)) {
        echo "<a class = 'next' href=\"".html::url($next['link'])."\">".Kohana::lang($next['label'])."</a>";
    }
    echo "<div class='float_end'></div>";
    echo "</div>";

    include APPPATH."/views/data/inc_actions.php";


?>

