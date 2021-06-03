/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


// written by Nickleus, 20151204
// API: http://api.jqueryui.com/sortable/
jQuery(document).ready(function($) {
    var jMenuContainer = $("#listItems");
    var jNLevelEmbeddedSortableList = jMenuContainer.find("ol");
    var embedLevel = 1; // N-level-deep embedding; default 1

    var sortableMenu = jNLevelEmbeddedSortableList.sortable({ // "main config options"
      axis: 'y',
      connectWith: jNLevelEmbeddedSortableList, // cross-list sorting/moving
      placeholder: "ui-state-highlight",
      forcePlaceholderSize: true,
      tolerance: "pointer",
      opacity: 1,
      revert: true,
      receive: function (event, ui) {

        var jUiItem = $(ui.item);

        // cancel sorting if the list-item-to-move already has more than N parents OR N-1 existing children (nested items)
        if (jUiItem.parents("li").length > embedLevel || jUiItem.find("li").length > (embedLevel - 1)) {
          $(ui.sender).sortable("cancel");
        }

      }
    });


    // add new item to menu
    $("#add_to_menu").click(function (event) {
      var title = $('#menu_title').val();
      alert(title);
      // create new jq item
      var jNewMenuItem = $('<li>'+title+'</li>');
      // create jq placeholder ul
      var innerUl = $('<ol class="newItem"></ol>');

      // init placeholder ul as sortable, w "main config options"
      innerUl.sortable(sortableMenu.sortable("option"));

      // insert the sortable placeholder ul into the "new item" li
      jNewMenuItem.append(innerUl);

      // insert the new item into the main sortable list
      jMenuContainer.find("ol.topmenu").append(jNewMenuItem);

      /*
      get the sortable "instance", so we can update option "connectWith".

      initially, i had this code (instead of the code currently impld below this block comment):
      ### PREVIOUS CODE

      sortableMenu.sortable("option", {
          connectWith: jMenuContainer.find("ul")
        });

      sortableMenu.sortable("refresh");

      ### END PREVIOUS CODE

      BUT in a real life impl of this, when i removed a menu item, then added a new, different item, i got this error:
      "Uncaught Error: cannot call methods on sortable prior to initialization; attempted to call method 'option'"

      so if i explicitly retrieve the instance first, seen below, then i don't get that error.
      */
      var sortableMenuInst = jNLevelEmbeddedSortableList.sortable("instance"); //

      // update/refresh list of ul-s to connectWith, so the new item gets included
      sortableMenuInst.option("connectWith", jMenuContainer.find("ol"));

      // refresh the main sortable list
      sortableMenuInst.refresh();

    });    
});
