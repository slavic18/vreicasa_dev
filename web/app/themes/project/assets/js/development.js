project.Behavior.fixFormPriceFields = function (context) {
    var $priceFieldsContainer = $('.js-price-submenu', context);
    if ($priceFieldsContainer.length) {
        function disableCheckboxes() {
            var $numberFields = $priceFieldsContainer.find('input[type="number"]'),
                closeCheckboxes = false;
            for (var i = 0, len = $numberFields.length; i < len; i++) {
                if ($numberFields.eq(i).val() !== '') {
                    closeCheckboxes = true;
                }
            }
            if (closeCheckboxes) {
                $priceFieldsContainer.find('.radio-container').addClass('inactive')
                    .find('input[type="radio"]')
                    .removeProp('checked')
                    .prop('disabled', true);
            } else {
                $priceFieldsContainer.find('.radio-container').removeClass('inactive')
                    .find('input[type="radio"]')
                    .removeProp('disabled');
            }
        }

        function disableNumberFields() {
            var $radioFields = $priceFieldsContainer.find('input[type="radio"]'),
                closeNumbers = false;
            for (var i = 0, len = $radioFields.length; i < len; i++) {
                if ($radioFields.eq(i).is(':checked')) {
                    closeNumbers = true;
                }
            }
            if (closeNumbers) {
                $priceFieldsContainer.find('input[type="number"]')
                    .prop('disabled', true);
            } else {
                $priceFieldsContainer.find('input[type="number"]')
                    .removeProp('disabled');
            }
        }

        $priceFieldsContainer.find('input[type="number"]').on('change', function () {
            disableCheckboxes();
        });
        $priceFieldsContainer.find('input[type="radio"]').on('change', function () {
            disableNumberFields();
        });
    }
}
project.Behavior.updateOrderValues = function (context) {
    var $links = $('.js-change-order', context);
    if ($links.length) {
        $links.on('click', function (e) {
            e.preventDefault();
            var $this = $(this),
                type = $this.data('order-type');
            if (typeof type !== 'undefined') {
                $('[name="form[orderby]"]').filter('[value="' + type + '"]').prop('checked', 'checked');
                $('.js-submit-form').trigger('click');
            }
        });
        // update active link
        $links.removeClass('active');
        $links.filter('[data-order-type="' + $('[name="form[orderby]"]').filter(':checked').val() + '"]').addClass('active');
    }
};
project.Behavior.updatePostsPerPagesValues = function (context) {
    var $links = $('.js-update-postsPerPage', context);
    if ($links.length) {
        $links.on('click', function (e) {
            e.preventDefault();
            var $this = $(this),
                type = $this.data('value');
            if (typeof type !== 'undefined') {
                $('[name="form[perPage]"]').filter('[value="' + type + '"]').prop('checked', 'checked');
                $('.js-submit-form').trigger('click');
            }
        });
        // update active link
        $links.removeClass('active');
        $links.filter('[data-value="' + $('[name="form[perPage]"]').filter(':checked').val() + '"]').addClass('active');
    }
};

project.Behavior.initSinglePostMap = function (context) {
    var $container = $('.js-init-map', context);
    if ($container.length) {
        var lat = $container.data('lat'),
            lng = $container.data('lng');
        if (lat && lng) {
            var latlng = new google.maps.LatLng(lat, lng),
                args = {
                    zoom: 17,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    scrollwheel: false,
                };
            var map = new google.maps.Map($container[0], args);
            map.markers = [];
            var marker = new google.maps.Marker({
                position: latlng,
                map: map,
            });
            map.markers.push(marker);
        }
    }
};

project.Behavior.addToFavorites = function (context) {
    $('.js-add-to-favorites', context).on('click', function (e) {
        e.preventDefault();
        var $this = $(this);
        $this.toggleClass('active');
        $.ajax({
            url: fruitframe.ajax_load_url,
            type: 'POST',
            dataType: 'json',
            data: {
                'action': '_toggleFavorite',
                'data': {
                    post_id: $this.data('post_id')
                },
            },
            success: function (data) {
                console.log(data);
                if (data.success != data.error) {

                }
            }
        });

    });
};

project.Behavior.initContactsMap = function (context) {
    var $map = $('.map-container', context);
    if ($map.length) {
        var lat = $map.data('lat'),
            lng = $map.data('lng'),
            args = {
                zoom: 13,
                center: new google.maps.LatLng(lat, lng),
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                scrollwheel: false,
            };
        var map = new google.maps.Map($map[0], args);
        map.markers = [];
        var latlng = new google.maps.LatLng(lat, lng);
        var marker = new google.maps.Marker({
            position: latlng,
            map: map,
            width: 10,
            icon: 'http://demo.acoperis.md/app/themes/project/assets/images/home/map-marker-icon.png',
        });

        map.markers.push(marker);
    }
}
project.Behavior.scrollToForm = function (context) {
    if (window.location && window.location.hash === '#scroll-to-form') {
        $('html,body').animate({scrollTop: $('.js-scroll-form').offset().top - 200}, 200);
    }
}
project.Behavior.masonryAddCls = function (context) {
    var $container = $('.grid > li:nth-child(5)', context),
        $container1 = $('.grid > li:nth-child(7)', context),
        $container2 = $('.grid > li:nth-child(8)', context),
        $container3 = $('.grid > li:nth-child(9)', context),
        $container4 = $('.grid > li:nth-child(10)', context);
    if ($container.length) {
        $container.addClass('costuri');
        $container1.addClass('calitatea');
        $container2.addClass('siguranta');
        $container3.addClass('invest');
        $container4.addClass('poveste');
    }
}

project.Behavior.addLightBox = function (context) {
    lightbox.option({
        'resizeDuration': 400,
        'wrapAround': true
    });
}
project.Behavior.addClassBox = function (context) {
    var $boxCont = $('.register-page-form > div:nth-child(3)', context);
    $boxCont.addClass('login-checkbox');
}
project.Behavior.appendBlock = function (context) {
    var $appndBlock = $('.bg-gray > .col-sm-7', context),
        $theAppndBlock = $('.bg-gray > .col-sm-5', context);
    if($(window).width() < 768){
        $appndBlock.append($theAppndBlock);
    }
    $(window).resize(function() {
        var width = $(document).width();
        if (width < 768) {
            $appndBlock.append($theAppndBlock);
        }
    });
}
project.Behavior.masonryAdd = function (context) {
    var $container = $('.grid', context);
    if ($container.length) {
        $container.masonry({
            // options
            itemSelector: 'li',
            columnWidth: 1,
            fitWidth: true
        });
    }
}
project.Behavior.removeClassCol = function (context) {
    // if ($(window).width() < 480) {
    //     $('.col-xs-6').removeClass('col-xs-6').addClass('col-xs-12');
    // }
}

project.Behavior.menuFixed = function (context) {
    var $header = $(".header-top", context);
    var $nav = $(".main-header");
    $(window).scroll(function () {
        if ($(this).scrollTop() > 216) {
            $header.addClass("pdd-body");
            $nav.addClass("main-fixed");
        }
        else {
            $header.removeClass("pdd-body");
            $nav.removeClass("main-fixed");
        }
    });
}

project.Behavior.openModalWin = function (context) {
    var $modal = $('#contact-form-modal', context);
    if ($modal.length) {
        $modal.iziModal({});
        $('.card-pachete').on('click', function (e) {
            e.preventDefault();
            $('#contact-form-modal').iziModal('open');
        })
    }
}
project.Behavior.openModaForm = function (context) {
    var $modalForm = $('#modal-form-window', context);
    if ($modalForm.length) {
        $modalForm.iziModal({});
        $('.show-modal-form').on('click', function (e) {
            e.preventDefault();
            $('#modal-form-window').iziModal('open');
            $('#modal-form-window').css({
                'visibility' : 'visible'
            });
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

project.Behavior.resetFormAp = function(context){
    var $resetFiltr = $('.reset-search-form', context);
    if($resetFiltr.length){
        $resetFiltr.on('click', function(){
            $("*[tag='tag']").val("").attr("checked", false);
        });
    }
}

project.Behavior.showfiltersSub = function (context) {
    var $formToggleBtn = $('.js-open-more-filters', context),
        $subMenuShow = $('.filters-container .ta-c .filter-list .filter-group-container .sub-menu', context);
    var $withSub = $('.with-sub-menu' , context),
        $subMenus = $('.sub-menu', context);
    if($withSub.length){
        $withSub.on('click', function () {
            $(this).toggleClass('show-sub-menus');
        });
    }
    if($formToggleBtn.length){
        $formToggleBtn.on('click', function(){
            $subMenuShow.toggle();
        });
    }
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




/**
 * Run All behaviors on document ready.
 */
jQuery(document).ready(function () {
    project.runBehaviors(document);
});