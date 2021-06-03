jQuery(function ($) {

    // Dropdown menu
    $(".wps-sidebar-dropdown > a").click(function (e) {
      e.preventDefault();
      $(".wps-sidebar-submenu").slideUp(200);
      if ($(this).parent().hasClass("active")) {
          $(".wps-sidebar-dropdown").removeClass("active");
          $(this).parent().removeClass("active");
      } else {
          $(".wps-sidebar-dropdown").removeClass("active");
          $(this).next(".wps-sidebar-submenu").slideDown(200);
          $(this).parent().addClass("active");
      }

    });

    //toggle sidebar
    $("#toggle-sidebar").click(function (e) {
      e.preventDefault();
      $("#wpcontent").toggleClass("toggled");
    });
    //Pin sidebar
    $("#pin-sidebar").click(function (e) {
      e.preventDefault();
      if ($("#wpcontent").hasClass("pinned")) {
          // unpin sidebar when hovered
          $("#wpcontent").removeClass("pinned");
          $("#wpfooter").removeClass("pinned");
          $("#wps-sidebar").unbind( "hover");
      } else {
          $("#wpcontent").addClass("pinned");
          $("#wpfooter").addClass("pinned");
          $("#wps-sidebar").hover(
              function () {
                  console.log("mouseenter");
                  $("#wpcontent").addClass("wps-sidebar-hovered");
                  $("#wpfooter").addClass("wps-sidebar-hovered");
              },
              function () {
                  console.log("mouseout");
                  $("#wpcontent").removeClass("wps-sidebar-hovered");
                  $("#wpfooter").addClass("wps-sidebar-hovered");
              }
          )

      }
    });

    //generate cookie for current menu item
    $('.wps-submenu-item > a').click(function (e) {
      var menuID = $(this).parent().parent().parent().parent().attr('id');
      Cookies.set('wps_current_menu', menuID, { expires: 1 });
    });
    $('.wps-menu-item > a').click(function (e) {
      var menuID = $(this).parent().attr('id');
      Cookies.set('wps_current_menu', menuID, { expires: 1 });
    });


    //toggle sidebar overlay
    $("#overlay").click(function () {
        $("#wpcontent").toggleClass("toggled");
    });

    // toggle background image
    $("#toggle-bg").change(function (e) {
        e.preventDefault();
        $('#wpcontent').toggleClass("sidebar-bg");
    });

    //custom scroll bar is only used on desktop
    if (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
        $(".wps-sidebar-content").mCustomScrollbar({
            axis: "y",
            autoHideScrollbar: true,
            scrollInertia: 300
        });
        $(".wps-sidebar-content").addClass("desktop");

    }
});
