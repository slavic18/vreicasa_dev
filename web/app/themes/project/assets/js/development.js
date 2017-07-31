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


project.Behavior.masonryAdd = function (context) {
    var $container = $('.grid', context);
    if ($container.length) {
        container.masonry({
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




/**
 * Run All behaviors on document ready.
 */
jQuery(document).ready(function () {
    project.runBehaviors(document);
});