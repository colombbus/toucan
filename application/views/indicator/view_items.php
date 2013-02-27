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
<script src="<?php echo url::file(Kohana::config('toucan.js_directory').'/effects.js'); ?>" type="text/javascript"></script>
<script src="<?php echo url::file(Kohana::config('toucan.js_directory').'/dragdrop.js'); ?>" type="text/javascript"></script>
<script type="text/javascript">
    function toggleDisplay(elementName) {
        var element = $(elementName);
        element.toggle();
    }

    function displayItem(id) {
        toggleDisplay('item_content_'+id);
    }

    function hideAll() {
        result = $$('.item_content');
        result.each(function(element){
            element.hide();
         });
    }

    function showAll() {
        result = $$('.item_content');
        result.each(function(element){
            element.show();
         });
    }
<?php
    if ($mayEdit) {
?>
    var editId;
    var newItem = false;
    var editionItem = false;

<?php
        if (isset($noItems)) {
?>
    var first_item = true;
<?php
        } else {
?>
    var first_item = false;
<?php
        }
        if (isset ($addUrl)) {
?>

    function addItem(url) {
        if (newItem|editionItem)
            window.alert('<?php echo addslashes(Kohana::lang($alreadyEditing));?>');
        else {
            if (url == undefined)
                url = '<?php echo html::url($addUrl)?>';
            if (first_item) {
                first_item = false;
                new Ajax.Updater('items', url , { method: 'get', evalScripts: true});
            } else {
                new Ajax.Updater('items', url, { method: 'get', evalScripts: true, insertion: 'bottom'});
            }
        }
    }

    function saveItem(id) {
        elementName = 'item_'+id;
        formName = 'form_item_'+id;
        if (id === "new") {
            $('item_new').id = 'item_new_old';
            newItem = false;
            new Ajax.Updater('items', '<?php echo html::url($addUrl)?>', {
                method: 'post',
                parameters:$(formName).serialize(true),
                onSuccess: function(transport) { $('item_new_old').remove();},
                evalScripts: true,
                insertion: 'bottom'
            });
        } else {
            editionItem = false;
            new Ajax.Updater(elementName, '<?php echo html::url($editUrl)?>/'+id , {
                method: 'post',
                parameters:$(formName).serialize(true),
                evalScripts: true
            });
        }

    }

<?php
        }
        if (isset($editUrl)) {
?>
    function editItem(id) {
        if (newItem|editionItem)
            window.alert('<?php echo addslashes(Kohana::lang($alreadyEditing));?>');
        else {
            editionItem = true;
            elementName = "item_"+id;
            editId = id;
            new Ajax.Updater(elementName, '<?php echo html::url($editUrl)?>/'+id, { method: 'get',  evalScripts: true});
        }
    }
<?php
        }
        if (isset ($deleteUrl)) {
?>

    function deleteItem(id, text) {
        elementName = "item_"+id;
        if (text===undefined) {
            text = "<?php echo addslashes(Kohana::lang($confirmDeletion)); ?>";
}        if (window.confirm(text)) {
            new Ajax.Request('<?php echo html::url($deleteUrl)?>/'+id, { method: 'get'});
            $(elementName).remove();
        }
    }

<?php
        }
?>

    function cancelEdit(id) {
        elementName = "item_"+id;
        if (id === "new") {
            $(elementName).remove();
            newItem = false;
        } else {
            editionItem = false;
            new Ajax.Updater(elementName, '<?php echo html::url($displayUrl)?>/'+id, { method: 'get',  onSuccess: function(transport) {}, evalScripts: true});
        }
    }

    function createSortable() {
<?php
        if (isset($reorderUrl)) {
?>
        Sortable.create('items',{onUpdate: function() {
            new Ajax.Request("<?php echo html::url($reorderUrl); ?>/", { method: "post",parameters: { data: Sortable.serialize("items") }});
            }, handle:'handle', scroll:window, zindex:2
        });
<?php
        }
?>
    }
<?php
    }
?>
<?php
    if (isset($isDraggable) && $isDraggable) {
?>
    document.observe("dom:loaded", function() {
        createSortable();
    });
<?php
    }
?>

<?php
    if (isset($noItems)) {
?>
        var updateRequired = false;
<?php
    } else {
?>
        var updateRequired = true;
<?php
    }
?>
    
<?php
        if (isset($updateUrl)) {
?>
    function updateItems() {
        if (updateRequired) {
            new Ajax.Updater("items", '<?php echo html::url($updateUrl)?>', { method: 'get',  evalScripts: true, insertion: 'bottom', onComplete: function(response){ updateItems();}});
        } else {
            $("loading").hide();
        }
    }
    document.observe("dom:loaded", function() {
        updateItems();
    });
<?php
        }
?>


</script>

<?php
    include APPPATH."/views/data/inc_description.php";
    //require_once "inc_display.php";
    if (isset($header)) {
        echo $header;
    }
    
    if (isset($hideItems)&&isset($showItems)) {
        echo form::button(array ('type'=>'button', 'name'=>"button_hide"), Kohana::lang($hideItems),"onClick=\"hideAll()\" class=\"toucan_button\"");
        echo form::button(array ('type'=>'button', 'name'=>"button_show"), Kohana::lang($showItems),"onClick=\"showAll()\" class=\"toucan_button\"");
    }

    echo "<ul id='items'>\n";
    if (isset($noItems)) {
        echo Kohana::lang($noItems);
        echo "</ul>\n";
    } else {
        echo "</ul><div id='loading'>Chargement...</div>";
    }
    include APPPATH."/views/data/inc_information.php";
    include APPPATH."/views/data/inc_actions.php";
?>
