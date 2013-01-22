$(document).ready(function(){
	
    //Sidebar Accordion Menu:
		
    $("#main-nav li ul").hide(); // Hide all sub menus
    $("#main-nav li a.current").parent().find("ul").slideToggle("fast"); // Slide down the current menu item's sub menu

    $("#main-nav li a.nav-top-item").click( // When a top menu item is clicked...
        function () {
            $(this).parent().find("ul").slideDown("normal"); //Show all the content in the sub menu clicked
            $(this).parent().siblings().find("ul").slideUp("normal"); // Slide up all sub menus except the one clicked
            return false;
        }
        );
		
    $("#main-nav li a.no-submenu").click( // When a menu item with no sub menu is clicked...
        function () {
            window.location.href=(this.href); // Just open the link instead of a sub menu
            return false;
        }
        );

    // Sidebar Accordion Menu Hover Effect:
		
    $("#main-nav li .nav-top-item").hover(
        function () {
            $(this).stop().animate({
                paddingRight: "16px"
            }, 180);
        },
        function () {
            $(this).stop().animate({
                paddingRight: "10px"
            });
        }
        );
});
