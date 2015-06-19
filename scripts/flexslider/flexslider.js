$jx = jQuery.noConflict();

$jx(window).load(function() {
    $jx('.flexslider').flexslider({
        animation: "slide",
        before: function(slider) {            
            $jx('.flex-active-slide h3').css('opacity', '0');
            $jx('.flex-active-slide .text').css('opacity', '0');            
        },
        after: function(slider) {
            $jx('.flex-active-slide h3').css('opacity', '1');
            $jx('.flex-active-slide .text').css('opacity', '1');
        }        
    });
});


