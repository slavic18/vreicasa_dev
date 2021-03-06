/* Main scripts file. */

window.project = {
    Behavior: {},
    Settings: {},
    Functions: {},
    runBehaviors: function () {
    }
};
jQuery.fn.extend({
    makeClass: function (className) {
        for (var i = 0, len = this.length; i < len; i++) {
            this.removeClass(className);
            this.addClass(className);
        }
    }
});

/**
 * Default (base) behavior
 */
project.Behavior.default = function (context) {
    jQuery('html').removeClass('no-js');
}

/**
 * Execute all Behaviors.
 */
project.runBehaviors = function (context) {
    if (typeof context == 'undefined') context = document;
    var behaviors = Object.keys(project.Behavior);
    for (var i = 0, len = behaviors.length; i < len; i++) {
        project.Behavior[behaviors[i]](context);
    }
}
/**
 * Run All behaviors on document ready.
 */
jQuery(document).ready(function () {
    project.runBehaviors(document);
});

/**
 * Copy input text to clipboard
 */
function select_all(obj) {
    var text_val = eval(obj);
    text_val.focus();
    text_val.select();
    if (!document.all) return; // IE only
    r = text_val.createTextRange();
    r.execCommand('copy');
}


function getWindowWidth() {
    var windowWidth = 0;
    if (typeof(window.innerWidth) == 'number') {
        windowWidth = window.innerWidth;
    } else {
        if (document.documentElement && document.documentElement.clientWidth) {
            windowWidth = document.documentElement.clientWidth;
        } else {
            if (document.body && document.body.clientWidth) {
                windowWidth = document.body.clientWidth;
            }
        }
    }
    return windowWidth;
}
project.Behavior.setEqualHeightBlocks = function (context) {
    var $el = $('.js-equal-height', context);
    if ($el.length) {
        $el.matchHeight({
            byRow: true,
            property: 'height',
            target: null,
            remove: false
        });
    }
}
project.Behavior.ratesSlider = function (context) {
    $(".owl-carousel").owlCarousel({
        nav: false,
        loop: true,
        dots: true,
        slideBy: 3,
        items: 1,
        smartSpeed: 1000,
        navText: ['<i class="fa fa-angle-left"></i>', '<i class="fa fa-angle-right"></i>'],
        responsive: {
            991: {
                items: 3,
                nav: true
            }
        }
    });
}
project.Behavior.showImage = function (context) {
    $('.more-image').click(function (e) {
        e.preventDefault();
        $('.more-image').toggle();
        $('.image-hide-block').toggle();
    })
}

/**
 * Mob menu
 */
project.Behavior.mobMenu = function (context) {
    var $mobMenuLink = $('.mob-menu-icon', context),
        $navbarMenuOverlay = $('.navbar-menu-overlay', context),
        $mobSidebar = $('.mob-sidebar'),
        $body = $('body'),
        $toggleMenuItems = $mobMenuLink.add($navbarMenuOverlay).add('.menu.menu-mob li a');

    if ($toggleMenuItems.length) {
        $toggleMenuItems.on('click', function (e) {
            event.preventDefault();
            $mobSidebar.toggleClass('mob-sidebar-opened');
            $mobMenuLink.toggleClass('mob-menu-icon-opened');
            $body.toggleClass('pos-fixed');
            $navbarMenuOverlay.toggle();
        });
    }
}

/**
 * Chosen select
 */
project.Behavior.chosenSelect = function (context) {
    var $select = $('select', context);
    var $selectItem = $();
    if ($select.length) {
        for (var i = 0, len = $select.length; i < len; i++) {
            $item = $select.eq(i);
            if ($item.hasClass('multiple')) {
                // chosen multiple
            } else if ($item.hasClass('custom')) {
                // custom chosen
            } else {
                // basic chosen no search
                $item.chosen({
                    disable_search: true,
                    width: '100%'
                })
            }
        }
    }
}


project.Behavior.masonryAdd = function (context) {
    $('.grid').masonry({
        // options
        itemSelector: 'li',
        columnWidth: 1,
        fitWidth: true
    });
}

project.Behavior.removeClassCol = function (context) {
    if ($(window).width() < 480) {
        $('.col-xs-6').removeClass('col-xs-6').addClass('col-xs-12');
    }
}


project.Behavior.menuFixed = function (context) {
    var mrgnHeader = $(".header-top");
    var nav = $(".main-header");
    $(window).scroll(function () {
        if ($(this).scrollTop() > 216) {
            mrgnHeader.addClass("mrgn-header");
            nav.addClass("main-fixed");
        }
        else {
            mrgnHeader.removeClass("mrgn-header");
            nav.removeClass("main-fixed");
        }
    });
}

project.Behavior.initIzimodal = function (context) {
    $("#modal").iziModal();
}

project.Behavior.openModalWin = function (context) {
    var $modal = $('#contact-form-modal', context);
    if ($modal.length) {
        $modal.iziModal({});
        $('.toggle-contact-modal').on('click', function (e) {
            e.preventDefault();
            $('#contact-form-modal').iziModal('open');
        });
    }
}
project.Behavior.openModaForm = function (context) {
    var $modalForm = $('#modal-form-window', context);
    if ($modalForm.length) {
        $modalForm.iziModal({});
        $('.show-modal-form').on('click', function (e) {
            e.preventDefault();
            $('#modal-form-window').iziModal('open');
        });
    }
}

project.Behavior.tabRating = function (context) {
    var $radio = $('.radio-container', context);
    $radio.on('click', function(e){
        var data = $(this).data('attr'),
            $el = $('.result[data-attr="'+data+'"]');
        if($el.length) {
            $el.addClass('result-vis');
            $el.prevAll().removeClass('result-vis');
            $el.nextAll().removeClass('result-vis');
        }
    });
}
project.Behavior.formSlider = function (context) {
    $(".js-research-carousel").owlCarousel({
        navigation : true,
        slideSpeed : 300,
        paginationSpeed : 400,
        singleItem: true,
        pagination: false,
        rewindSpeed: 500
    });
}

project.Behavior.modalSliderForm = function (context) {
    $('.show-modal-form').on('click', function (e) {
        setTimeout(function(){
            $('.form-slick').slick({
                infinite: true,
                speed: 300,
                slidesToShow: 1,
                adaptiveHeight: true
            });
            var $btnNext = $('.slick-next', context),
                $btnPrev = $('.slick-prev', context);
            if($btnNext.length){
                $btnNext.on('click', function () {
                    $btnPrev.css({
                        "visibility" : "visible"
                    });
                });
            }
        }, 10);
    });
}
