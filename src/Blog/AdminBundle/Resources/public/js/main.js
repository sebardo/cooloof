(function($) {
    $('span[data-action="minifyMenu"]').on('click', function() {
        if ($('body').hasClass('minified')) {
            createCookie('menu-status', 'show', 7);
        }
        else {
            createCookie('menu-status', 'hide', 7);
        }
    });

    (function($) {
        setInterval(function() {$('.flash-box').hide('slow')}, 5000);
    })(jQuery);

    $('.evo-spinner .ui-spinner-up').on('click', function(e) {
        e.preventDefault();
        var quantity = parseInt($(".ui-spinner input").val());
        if (isNaN(quantity)) {
            quantity = 0;
        }
        quantity++;
        $(".ui-spinner input").val(quantity);
    });

    $(".ui-spinner input").keydown(function (e) {
        // Allow: backspace, delete, tab, escape, enter and .
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                // Allow: Ctrl+A
            (e.keyCode == 65 && e.ctrlKey === true) ||
                // Allow: home, end, left, right
            (e.keyCode >= 35 && e.keyCode <= 39)) {
            // let it happen, don't do anything
            return;
        }
        // Ensure that it is a number and stop the keypress
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });

    $('.evo-spinner .ui-spinner-down').on('click', function(e) {
        e.preventDefault();
        var quantity = parseInt($(".ui-spinner input").val());
        if (isNaN(quantity)) {
            quantity = 1;
        }
        if (quantity > 1) {
            quantity--;
        }
        $(".ui-spinner input").val(quantity);
    });

    $('#btn-change-days').on('click', function(e) {
        e.preventDefault();
        location.href = $(this).attr('href').replace('XXXX',$(".ui-spinner input").val());
    })

})(jQuery)

function createCookie(name, value, days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for (var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
