$( document ).ready(function() {
    $(".burger").click(function() {
        $(".menunav").toggle();
    });

    initmenu();
});

$(window).resize(resizemenu) ;

var lastwidth = null;

function gomobile() {
     console.log("go mobile");
    $(".menunav").hide();
    $(".menunav").addClass("menu-mobile");

    $(".menunav" ).appendTo(".mobilemenu");
    $(".burger").show();
}

function godesktop() {
     console.log("go desktop");
    $(".menunav").show();
    $(".menunav").removeClass("menu-mobile");

    $(".menunav" ).appendTo(".menunavdesktopanchor");
    $(".burger").hide();
}
function resizemenu () {
    if (lastwidth > 600 && $( window ).width() <= 600 ) {
        gomobile();
    } 
    if (lastwidth < 600 && $( window ).width() >= 600 ) {
        godesktop();
    }
    lastwidth = $( window ).width();
}
function initmenu() {
    lastwidth = $( window ).width();
    if ($( window ).width() <= 600 ) {
        gomobile();
    } 
    if ($( window ).width() >= 600 ) {
        godesktop();
    }
}
