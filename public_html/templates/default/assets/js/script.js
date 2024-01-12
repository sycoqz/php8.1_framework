$(function () {
    //------------------- Swipers -------------------//
    var mainSlider = new Swiper('.slider__container', {
        pagination: {
            el: '.slider__pagination',
            type: 'fraction',
        },
        navigation: {
            nextEl: '.slider__controls._next',
            prevEl: '.slider__controls._prev',
        },
    });

    var partnersSlider = new Swiper('.partners__container', {
        navigation: {
            nextEl: '.partners__controls._next',
            prevEl: '.partners__controls._prev',
        },
        slidesPerView: 1,
        spaceBetween: 20,
        loop: true,
        breakpoints: {
            1601: {
                slidesPerView: 8
            },
            1367: {
                slidesPerView: 7
            },
            1281: {
                slidesPerView: 6
            },
            1025: {
                slidesPerView: 5
            },
            769: {
                slidesPerView: 4
            },
            561: {
                slidesPerView: 3
            },
            421: {
                slidesPerView: 2
            },
        }
    });

    var indexOffersSlider= null;

    var indexOffersSliderOptions = {
        navigation: {
            nextEl: '.offers__controls._next',
            prevEl: '.offers__controls._prev',
        },
        slidesPerView: 1,
        spaceBetween: 20,
        wrapperClass: 'offers__tabs_wrapper',
        slideClass: 'offers__tabs_card',
        watchOverflow: true,
        breakpoints: {
            1251: {
                slidesPerView: 4,
            },
            769: {
                slidesPerView: 3
            },
            561: {
                slidesPerView: 2
            }
        }
    };
    indexOffersSlider =  new Swiper('.offers__tabs_container', indexOffersSliderOptions);
    new Swiper('.active .offers__tabs_container', indexOffersSliderOptions);


    //------------------- Tabs Mainpage -------------------//
    $('ul.offers__tabs_header').on('click', 'li:not(.active)', function() {
        $(this)
            .addClass('active').siblings().removeClass('active')
            .closest('div.offers__tabs').find('div.offers__tabs_content').removeClass('active').eq($(this).index()).addClass('active');
        var index = $(this).index();
        indexOffersSlider[index].slideTo(0);
        indexOffersSlider[index].update();

    });
    //------------------- Burger Sidebar  -------------------//
    $('.burger-menu').on('click', function () {
        var burgerHidden = $('.header__menu').hasClass('_hidden');
        var callbackHidden = $('.header__callback').hasClass('_hidden');
        if(burgerHidden) {//бургер скрыт
            if(!callbackHidden) {//но открыта обратка
                $('.header__callback').addClass('_hidden');//скроем обратку
                if ($(window).width() <= 1024) {//на мобилке
                    $('.overlay').removeClass('_visible');
                    $('.header__sidebar').removeClass('_bg-opened');
                }
            }
            else {//обратка закрыта
                $('.overlay').addClass('_visible');//покажем оверлей тк его нет
                if($(window).width() <= 1024) {
                    $('.header__sidebar').addClass('_bg-opened')//на бургере повернем крестик
                    $('.header__menu').removeClass('_hidden');
                }
            }
            if($(window).width() > 1024){
                $('.header__menu').removeClass('_hidden');//в любом случае вызовем меню на десктопе
            }
        }
        else {//бургер открыт
            $('.overlay').removeClass('_visible');//скроем оверлей
            if($(window).width() <= 1024) {
                $('.header__sidebar').removeClass('_bg-opened')//свернем крестик в бургер
            }
            $('.header__menu').addClass('_hidden');//скроем меню
        }
    });
    $('.header__menu_close').on('click', function () {
        $('.overlay').removeClass('_visible');
        if($(window).width() <= 1024) {
            $('.header__sidebar').removeClass('_bg-opened')
        }
        $('.header__menu').addClass('_hidden');
    });
    //------------------- Callback Popup  -------------------//
    $('.js-callback').on('click', function () {
        $('.overlay').addClass('_visible');
        if($(window).width() <= 1024) {
            $('.header__sidebar').addClass('_bg-opened')
        }
        $('.header__callback').removeClass('_hidden');
    });
    $('.header__callback_close').on('click', function () {
        $('.overlay').removeClass('_visible');
        if($(window).width() <= 1024) {
            $('.header__sidebar').removeClass('_bg-opened')
        }
        $('.header__callback').addClass('_hidden');
    });
    //------------------- Overlay Events  -------------------//
    $('.overlay').on('click', function () {
        var burgerHidden = $('.header__menu').hasClass('_hidden');
        var callbackHidden = $('.header__callback').hasClass('_hidden');

        if(!burgerHidden) {
            $('.header__menu').addClass('_hidden');
        }
        if(!callbackHidden) {
            $('.header__callback').addClass('_hidden');
        }

        $('.overlay').removeClass('_visible');
        if($(window).width() <= 1024) {
            $('.header__sidebar').removeClass('_bg-opened')
        }
    });
    //------------------- Masked Inputs  -------------------//
    $('.js-mask-phone').mask("+7 (999) 999-99-99");

    //------------------- Horizontal Scroll -------------------//
    var controller = new ScrollMagic.Controller();

    if($(window).width() > 1024) {
        var timeline = new TimelineMax();
        timeline
            .to($('.horizontal__wrapper'), 1, {xPercent: '-50'});

        var horizontalScroll = new ScrollMagic.Scene({
            triggerElement: '.horizontal',
            triggerHook: 'onEnter',
            offset: $('.horizontal__wrapper').height(),
            duration: '100%'
        })
            .setTween(timeline)
            .setPin(".horizontal__wrapper")
            .addTo(controller);
    }



    //------------------- Sticky Search -------------------//
    if ($(window).width() > 1024 && $('.news').length > 0) {
        var heightToFooter = $('.news').offset().top;

        var stickySearch = new ScrollMagic.Scene({
            triggerElement: '.footer',
            triggerHook: 'onEnter'
        })
            .setClassToggle('.search', '_unpin')
            .addTo(controller);
    }
    //------------------- Add Desktop Animate Classes -------------------//
    if ($(window).width() > 1024) {
        $('.search').addClass(['animated', 'bounceInLeft']);
        $('.header__sidebar').addClass(['animated', 'bounceInUp']);
    }
});
