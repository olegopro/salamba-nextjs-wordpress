<?php
/*
 * WPSPowerbox
 * @author   AcmeeDesign
 * @url     https://acmeedesign.com
*/

if ( !empty( $menu_data ) ) {
  $menu_data = str_replace( "'", "\\'", $menu_data );
$js_data = 'jQuery(document).ready(function($) {
    "use strict";
    var $domenu            = $("#domenu-0"),
        domenu             = $("#domenu-0").domenu(),
        $outputContainer   = $("#domenu-0"),
        $jsonOutput        = $outputContainer.find(".jsonOutput");
  $("#domenu-0").domenu(
    {
    data: ' . "'" . $menu_data . "'" . ',
    maxDepth: 1
    }
  )
  .onCreateItem(function(blueprint) {
    var ele = $(blueprint).find("input[name=\'title\']").attr("data-default-value");
    var value = $(blueprint).find("input[name=\'title\']").val();
    if(value != "")
    {
      var out = this._plugin.resolveToken(ele, "");
    }
    var num = (this._plugin.incrementIncrement)-1;
    var icon_val = $(blueprint).find("input[type=\'hidden\']").val();

    if(icon_val != "")
    {
    blueprint.find(".divicon").attr("data-target", "#menu-icon-for-"+num).addClass(" "+icon_val.replace("|", " "));
    }

    blueprint.find(".divicon").attr("data-target", "#menu-icon-for-"+num);
    blueprint.find(".txticon").attr("id", "menu-icon-for-"+num);
    blueprint.find(".txticon").removeClass("txticon");
    blueprint.find(".divicon").removeClass("divicon");

    var editmenuBtn = $(blueprint).find(".edit-menu-btn");
    editmenuBtn.click(function() {
      blueprint.find(".dd3-content span").first().click();
    });

    var wpsmenutype = $(blueprint).find(".wps-menu-type option:selected").val();
    if(wpsmenutype == "3") {
      blueprint.find(".dd3-content .menu-separator").css("display", "inline-block");
      blueprint.find(".dd3-content .menu-group-title").css("display", "none");
    }
    else if(wpsmenutype == "2") {
      blueprint.find(".dd3-content .menu-group-title").css("display", "inline-block");
      blueprint.find(".dd3-content .menu-separator").css("display", "none");
    }
    else {
      blueprint.find(".dd3-content .menu-group-title,.dd3-content .menu-separator").css("display", "none");
    }
  })
  .parseJson()
  .on(["onItemCollapsed", "onItemExpanded", "onItemAdded", "onSaveEditBoxInput", "onItemDrop", "onItemDrag", "onItemRemoved", "onItemEndEdit"], function(a, b, c) {
    $jsonOutput.val(domenu.toJson());
  })
  .onItemStartEdit(function($item) {
    $(".wps-save-action").prop( "disabled", true );
    $(".wps-menuform-message").slideDown(200);
    $(".wps-menu-save-note").slideDown(200);
  })
  .onItemEndEdit(function(blueprint) {
    $(".wps-save-action").prop( "disabled", false );
    var wpsmenutype = $(blueprint).find(".wps-menu-type option:selected").val();
    if(wpsmenutype == "3") {
      blueprint.find(".dd3-content .menu-separator").css("display", "inline-block");
      blueprint.find(".dd3-content .menu-group-title").css("display", "none");
    }
    else if(wpsmenutype == "2") {
      blueprint.find(".dd3-content .menu-group-title").css("display", "inline-block");
      blueprint.find(".dd3-content .menu-separator").css("display", "none");
    }
    else {
      blueprint.find(".dd3-content .menu-group-title,.dd3-content .menu-separator").css("display", "none");
    }
  })
  .onItemAdded(function(blueprint) {
    blueprint.find(".dd3-content .menu-group-title,.dd3-content .menu-separator").css("display", "none");
  })

  $(".dd-new-item").on("click", function(){
    $(".icon-picker").iconPicker();
  });

  $(".wps-save-action").prop( "disabled", false );

});';
}
else {
$js_data = 'jQuery(document).ready(function($) {
    "use strict";
    var $domenu            = $("#domenu-0"),
        domenu             = $("#domenu-0").domenu(),
        $outputContainer   = $("#domenu-0"),
        $jsonOutput        = $outputContainer.find(".jsonOutput");
  $("#domenu-0").domenu(
    {
    maxDepth: 1
    }
  )
  .onCreateItem(function(blueprint) {
    var ele = $(blueprint).find("input[name=\'title\']").attr("data-default-value");
    var value = $(blueprint).find("input[name=\'title\']").val();
    if(value != "")
    {
      var out = this._plugin.resolveToken(ele, "");
    }
    var num = (this._plugin.incrementIncrement)-1;

    var icon_val = $(blueprint).find("input[type=\'hidden\']").val();

    if(icon_val != "")
    {
    blueprint.find(".divicon").attr("data-target", "#menu-icon-for-"+num).addClass(" "+icon_val.replace("|", " "));
    }

    blueprint.find(".divicon").attr("data-target", "#menu-icon-for-"+num);
    blueprint.find(".txticon").attr("id", "menu-icon-for-"+num);
    blueprint.find(".txticon").removeClass("txticon");
    blueprint.find(".divicon").removeClass("divicon");

    var editmenuBtn = $(blueprint).find(".edit-menu-btn");
    editmenuBtn.click(function() {
      blueprint.find(".dd3-content span").first().click();
    });
  })
  .on(["onItemCollapsed", "onItemExpanded", "onItemAdded", "onSaveEditBoxInput", "onItemDrop", "onItemDrag", "onItemRemoved", "onItemEndEdit"], function(a, b, c) {
    $jsonOutput.val(domenu.toJson());
  })
  .onItemStartEdit(function($item) {
    $(".wps-save-action").prop( "disabled", true );
    $(".wps-menuform-message").slideDown(200);
    $(".wps-menu-save-note").slideDown(200);
  })
  .onItemEndEdit(function(blueprint) {
    $(".wps-save-action").prop( "disabled", false );
    var wpsmenutype = $(blueprint).find(".wps-menu-type option:selected").val();
    if(wpsmenutype == "3") {
      blueprint.find(".dd3-content .menu-separator").css("display", "inline-block");
      blueprint.find(".dd3-content .menu-group-title").css("display", "none");
    }
    else if(wpsmenutype == "2") {
      blueprint.find(".dd3-content .menu-group-title").css("display", "inline-block");
      blueprint.find(".dd3-content .menu-separator").css("display", "none");
    }
    else {
      blueprint.find(".dd3-content .menu-group-title,.dd3-content .menu-separator").css("display", "none");
    }
  })

  $(".dd-new-item").on("click", function(){
    $(".icon-picker").iconPicker();
  });

  $( ".wps-menuset-title" ).keyup(function() {
    var menusettitle = $(this).val();
    if(menusettitle != "") {
      $(".wps-save-action").prop( "disabled", false );
    }
    else {
      $(".wps-save-action").prop( "disabled", true );
    }
  });

});';
}

wp_add_inline_script('wpspb-menujs', $js_data);
