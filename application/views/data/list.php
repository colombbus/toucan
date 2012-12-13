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
    include "inc_description.php";
?>

    <?php if (isset($search)) {
     ?>
     <script type="text/javascript">
     function toggleSearch() {
    	    $('search').toggle();
     }

     </script>
     <a class="table_search" href="javascript:toggleSearch()"><?php echo Kohana::lang('search.title');?></a>
        <?php
            if (isset($showSearch)&&$showSearch) {
                echo "<form id='search' action='javascript:setSearch()'>";
            } else {
                echo "<form id='search' action='javascript:setSearch()' style='display:none;'>";
            }
        ?>
        <fieldset>
            <table class="toucan_edition">
            <?php
                foreach ($search as $item) {
                    echo "<tr><td class='edition_label'>";
                    echo form::label($item['name'],Kohana::lang($item['text']));
                    echo "</td><td class='edition_entry'>";
                    if (isset($item['value']))
                        echo form::input($item['name'], $item['value']);
                    else
                        echo form::input($item['name']);
                    echo "</tr>";
                }
                echo "<tr><td></td><td>";
                if (isset($dataName))
                    echo form::hidden('form_list_search',$dataName);
                else
                    echo form::hidden('form_list_search',1);

                echo form::button(array ('type'=>'button', 'id'=>"search_cancel"), Kohana::lang('search.cancel'),"onClick='clearSearch()' class=\"toucan_button\"");
                echo form::button(array ('type'=>'submit', 'id'=>"search_submit"), Kohana::lang('search.submit'), "class=\"toucan_button\"");
                echo "</td></tr></table>";

            ?>
        </table>
        </fieldset>
        </form>
        <script type="text/javascript">

        function setSearch() {
            // get search parameters

            new Ajax.Request('<?php echo html::url("list/setSearch")?>',
                    {
                        method:'post',
                        parameters:$('search').serialize(true),
                        onSuccess: function(transport){
            	            updateList(baseUrl);
                        },
                        onFailure: function(){
                            }
                    });
        }

        function clearInput(inputName) {
            inputName.clear();
        }

        function clearSearch() {
            new Ajax.Request('<?php echo html::url("list/clearSearch");?>',
                    {
                        method:'post',
                        <?php if (isset($dataName)) { ?>
                        parameters:'form_list_search=<?php echo $dataName?>',
                        <?php } ?>
                        onSuccess: function(transport){
                            $('search').getInputs('text').each(clearInput);
                            updateList(baseUrl);
                        },
                        onFailure: function(){
                            }
                    });
        }

        </script>
    <?php } ?>
    <script type='text/javascript'>
     previousRow=0;
     currentRow=-1;

     function unselectRow(event) {
         row = event.findElement('tr');
         row.removeClassName('row_over');
     }

     function selectRow(event) {
         row = event.findElement('tr');
         row.addClassName('row_over');
     }

     function setRow(element, index) {
         element.observe('mouseover',selectRow);
         element.observe('mouseout',unselectRow);
     }

     function initRows() {
         $$('tr.table_row').each(setRow);
     }
    </script>
    <table class='toucan_table'>
        <thead>
            <tr>
        <?php
            $currentOrder = 1;
            foreach ($headers as $header) {
                echo "<th class='table_header'>";
                if (isset($header['name'])) {
                    if (strcmp($header['name'], $sortingName)==0) {
                        if ($sortingOrder==0) {
                            echo "<div id='filter_".$header['name']."' class='table_filter'>".html::image(array("src"=>Kohana::config('toucan.images_directory')."/up.png"))."</div>";
                            $currentOrder = 0;
                        }
                        else {
                            echo "<div id='filter_".$header['name']."' class='table_filter'>".html::image(array("src"=>Kohana::config('toucan.images_directory')."/down.png"))."</div>";
                        }
                    } else {
                        echo "<div id='filter_".$header['name']."' class='table_filter'></div>";
                    }
                    echo "<a href=\"javascript:setFilter('".$header['name']."')\">";
                    echo Kohana::lang($header['text']);
    				echo "</a>";
                } else {
                    echo Kohana::lang($header['text']);
                }
                echo "</th>";
            }
            if (isset($listIcons)) {
                for ($i=0;$i<$listIcons;$i++)
                    echo "<th class='table_header table_actions'></th>";
            }
        ?>
            </tr>
        </thead>
        <tbody id='list_content'></tbody>
    </table>
    <script language="javascript">

    <?php
        if (isset($sortingName)) {
    ?>
    var currentFilter = "<?php echo $sortingName; ?>";
    var previousOrder = <?php echo $currentOrder;?>;
    var order = <?php echo $currentOrder;?>;
    var oldFilter = null;
    var filterRequest =  false;

    function setFilter(name) {
        if (!filterRequest) {
            filterRequest = true;
            oldFilter = currentFilter;
            previousOrder = order;
            if (name == currentFilter)
            {
                order = Math.abs(order-1);
            }
            else
            {
                currentFilter = name;
                order = 1;
            }
            new Ajax.Request('<?php echo html::url("list/setSorting");?>/'+name+'/'+order,
            {
                method:'get',
                onSuccess: function(transport){
                updateList(baseUrl);
                },
                onFailure: function(){
                    currentFilter = oldFilter;
                    order = previousOrder;
                    filterRequest = false;
                }
            });
        }
    }

    function updateFilter() {
        $("filter_"+oldFilter).update("");
        if (order == 1) {
        	$("filter_"+currentFilter).update('<?php echo html::image(array("src"=>Kohana::config('toucan.images_directory')."/down.png"))?>');
        } else {
        	$("filter_"+currentFilter).update('<?php echo html::image(array("src"=>Kohana::config('toucan.images_directory')."/up.png"))?>');
        }
        filterRequest = false;
    }
<?php
        }
?>
    var baseUrl = "<?php echo html::url($listUrl)?>";

    function updateList(url) {
        new Ajax.Updater('list_content', url, { method: 'get', onSuccess: function(transport) {updateFilter();}, evalScripts: true });
    }

    function init() {
        updateList(baseUrl);
    }

    Event.observe(window, 'load', init);
    </script>
 <?php
    include "inc_information.php";
?>
<?php
    include "inc_actions.php";
?>
