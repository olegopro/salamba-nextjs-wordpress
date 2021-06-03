jQuery(document).ready(function($) {
    'use strict';

    $("#manage_fonts_primary_variants").select2({placeholder: 'Select an option'});
    $('#manage_fonts_primary_font').on('change', function(){

        var fontname = $('#manage_fonts_primary_font').val();

        $("#manage_fonts_primary_variants").find('option').remove();

        var ajax_url = wpspb_admin_ajax_url.admin_ajax_url;

        var variants = $("#manage_fonts_primary_variants");

        if(fontname.length > 0){
           $.ajax({
                url: wpspb_admin_ajax_url.admin_ajax_url,
                type: "POST",
                data: {
                    action: 'showfontvariants',
                    security: wpspb_admin_ajax_url.nonce,
                    fontname: fontname,
                },
                dataType: "json",
                success: function (response) {
                    variants.css('width', '180');
                   // variants.append("<option value=''>--Choose Variants--</option>");
                    $.each(response['variants'], function(ind, data){
                        if ($.inArray(data, response['checked'])!='-1') {
                            var selected = "selected";
                        } else {
                            var selected = "";
                        }
                        variants.append("<option value="+data+" "+selected+">" + data + "</option>");
                    });

                    $(variants).select2({
                            placeholder: 'Select an option'
                    });


                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr)
                }
            });
        }
    });

    $("#manage_fonts_secondary_variants").select2({placeholder: 'Select an option'});
    $('#manage_fonts_secondary_font').on('change', function(){

        var fontname = $('#manage_fonts_secondary_font').val()

        $("#manage_fonts_secondary_variants").find('option').remove();

        var ajax_url = wpspb_admin_ajax_url.admin_ajax_url;

        var variants = $("#manage_fonts_secondary_variants");

        if(fontname.length > 0){
           $.ajax({
                url: wpspb_admin_ajax_url.admin_ajax_url,
                type: "POST",
                data: {
                    action: 'showfontvariants',
                    security: wpspb_admin_ajax_url.nonce,
                    fontname: fontname,
                },
                dataType: "json",
                success: function (response) {
                    variants.css('width', '180');
                   // variants.append("<option value=''>--Choose Variants--</option>");
                    $.each(response['variants'], function(ind, data){
                        if ($.inArray(data, response['checked'])!='-1') {
                            var selected = "selected";
                        } else {
                            var selected = "";
                        }
                        variants.append("<option value="+data+" "+selected+">" + data + "</option>");
                    });

                    $(variants).select2({
                        placeholder: 'Select an option'
                    });


                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr)
                }
            });
        }
    });

    $('.redirect-users .select_redirect_page').on('change', function (e) {
        var redirect_page = $("option:selected", this).val();
        //alert(redirect_page);
        if(redirect_page == "custom_url") {
            $(this).parent().next('div.custom_url').show('fast');
        }
        else {
            $(this).parent().next('div.custom_url').hide('fast');
        }
    });



    $('#add_to_menu').on('click', function(e){
        var title = $('#menu_title').val();
        var url = $('#menu_url').val();
        var num = 0;
        if($("#listItems ol#sortable li").length > 0){
            num = $("#listItems ol#sortable li").length ;
        }
        num = parseInt(num) + 1;

        var inputtitle = '<div class="menu-title"><label for="input-title-top-li-'+num+'">Title</label><input type="text" class="customtitles" name="manage[wp_custom_admin_menu]['+num+'][menu_title]" id="input-title-top-li-'+num+'" value="'+title+'"/></div>';
        var inputurl = '<div class="menu-url"><label for="input-url-top-li-'+num+'">URL</label><input type="text" name="manage[wp_custom_admin_menu]['+num+'][menu_url]" id="input-url-top-li-'+num+'" value="'+url+'"/></div>';
        var edithref = '<a href="javascript:void(0)" class="wps-edit-expand"><i class="fa fa-chevron-down" aria-hidden="true"></i> <span>Edit</span></a>'
        var delhref = '<a href="javascript:void(0)" class="wps-remove-menu"><i class="fa fa-chevron-down" aria-hidden="true"></i> <span>Remove</span></a>'

        $("#listItems ol#sortable").append('<li id="top-li-'+num+'"><div class="alter-sort-list"><span class="menu_title">'+title+'</span>'+edithref+delhref+'<div class="alter-menu-contents">'+inputtitle +'' + inputurl + '</div></div>' + '</li>');

        reindexelements();
        sortablelist();
        $('#menu_title').val('');
        $('#menu_url').val('');
        $('#menu_title').focus();
    });
    if($("#listItems ol#sortable li").length > 0){
        sortablelist();
    }

    $(document).on('click','a.wps-edit-expand', function(e) {
        e.preventDefault();
        $(this).nextAll('.alter-menu-contents:first').slideToggle('fast');
    });

    $(document).on('keyup', '#listItems ol#sortable li input.customtitles',function(){
       var $li = $(this).parent("div").parent("div").parent("div").children("span");
       $li.html($(this).val());
    });

    $(document).on('click','.wps-remove-menu', function(){
        $(this).parent("div").parent("li").find("li").each(function(k){
            $(this).remove();
        });
        var id = $(this).parent("div").parent("li");
        $(id).remove();
    });

    $('.top_chk').on('click', function(){
        var flag = this.checked;
        $(this).parents("li").children("ol").find("input[type=checkbox]").each(function(){
            $(this).attr('checked', flag);
        });
    });

    $('.sub_chk').on('click', function(){
        var flag = this.checked;
        if(this.checked === false){
            $(this).parents("li").parents("li").find(".top_chk").each(function(){
                $(this).removeAttr('checked');
            });

        }

    });



});


function sortablelist()
{
    'use strict';

    $('.sortable').nestedSortable({
            forcePlaceholderSize: true,
            items: 'li',
            handle: 'div',
            placeholder: 'menu-highlight',
            listType: 'ol',
            maxLevels: 2,
            opacity: .6,
            update: function(event, ui) {
                reindexelements();
            }
    });
}

function reindexelements()
{
    'use strict';

    $('#listItems ol#sortable > li').each(function(i, li) {
        $(li).attr("id", "top-li-"+i);
        $(li).find( "input:eq(0)" ).attr('name', 'manage[wp_custom_admin_menu]['+i+'][menu_title]');
        $(li).find( "input:eq(1)" ).attr('name', 'manage[wp_custom_admin_menu]['+i+'][menu_url]');
    });

    $('#listItems ol#sortable li').each(function(i, li) {
        var $this = $(li);
        $this.find("li").each(function(k){
            var ind = $this.index();//$(this).parent("ul").parent("li").index();
            $(this).attr("id", "top-li-"+ind+"-sub-"+k);
            $(this).find( "input:eq(0)" ).attr('name', 'manage[wp_custom_admin_menu]['+ind+'][submenu]['+k+'][menu_title]');
            $(this).find( "input:eq(1)" ).attr('name', 'manage[wp_custom_admin_menu]['+ind+'][submenu]['+k+'][menu_url]');
        });
    });
}
