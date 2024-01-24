!function (e) {
    var t = {};

    function i(n) {
        if (t[n]) return t[n].exports;
        var o = t[n] = {i: n, l: !1, exports: {}};
        return e[n].call(o.exports, o, o.exports, i), o.l = !0, o.exports
    }

    i.m = e, i.c = t, i.d = function (e, t, n) {
        i.o(e, t) || Object.defineProperty(e, t, {enumerable: !0, get: n})
    }, i.r = function (e) {
        "undefined" != typeof Symbol && Symbol.toStringTag && Object.defineProperty(e, Symbol.toStringTag, {value: "Module"}), Object.defineProperty(e, "__esModule", {value: !0})
    }, i.t = function (e, t) {
        if (1 & t && (e = i(e)), 8 & t) return e;
        if (4 & t && "object" == typeof e && e && e.__esModule) return e;
        var n = Object.create(null);
        if (i.r(n), Object.defineProperty(n, "default", {
            enumerable: !0,
            value: e
        }), 2 & t && "string" != typeof e) for (var o in e) i.d(n, o, function (t) {
            return e[t]
        }.bind(null, o));
        return n
    }, i.n = function (e) {
        var t = e && e.__esModule ? function () {
            return e.default
        } : function () {
            return e
        };
        return i.d(t, "a", t), t
    }, i.o = function (e, t) {
        return Object.prototype.hasOwnProperty.call(e, t)
    }, i.p = "", i(i.s = 5)
}([function (e, t) {
    e.exports = jQuery
}, function (e, t) {
    new Swiper(".index-banner-slider", {
        speed: 2e3,
        direction: "vertical",
        grabCursor: !0,
        autoplay: !0,
        loop: !0,
        parallax: !0,
        breakpoints: {
            980: {
                pagination: {
                    el: ".index-banner__pagination", renderBullet: function (e, t) {
                        return e < 10 ? '<span class="' + t + '">0' + (e + 1) + "</span>" : '<span class="' + t + '">' + (e + 1) + "</span>"
                    }
                }
            }
        }
    });
    $((function () {
        $(window).width() > 1365 ? $(".index-banner-item__img-picture").attr("data-swiper-parallax", "854") : $(window).width() > 979 ? $(".index-banner-item__img-picture").attr("data-swiper-parallax", "560") : $(window).width() > 639 ? $(".index-banner-item__img-picture").attr("data-swiper-parallax", "380") : $(window).width() > 479 ? $(".index-banner-item__img-picture").attr("data-swiper-parallax", "280") : $(".index-banner-item__img-picture").attr("data-swiper-parallax", "220")
    }));
    new Swiper(".index-certificates__slider", {
        speed: 2e3,
        grabCursor: !0,
        autoplay: !0,
        slidesPerView: 1,
        spaceBetween: 5,
        navigation: {nextEl: ".index-certificates__button-next", prevEl: ".index-certificates__button-prev"},
        breakpoints: {
            1440: {slidesPerView: 4, spaceBetween: 30},
            1101: {slidesPerView: 3, spaceBetween: 30},
            981: {slidesPerView: 4, spaceBetween: 20},
            641: {slidesPerView: 3},
            481: {slidesPerView: 2}
        }
    });
    var i = new Swiper(".card-main-gallery-thumb__container", {
        spaceBetween: 22,
        slidesPerView: 2,
        direction: "horizontal",
        breakpoints: {641: {slidesPerView: 3, direction: "vertical"}, 481: {slidesPerView: 2, direction: "vertical"}}
    });
    new Swiper(".card-main-gallery-slider__container", {
        slidesPerView: 1,
        thumbs: {swiper: i}
    }), new Swiper(".card-slider-slider__container", {
        speed: 2e3,
        grabCursor: !0,
        slidesPerView: 1,
        spaceBetween: 18,
        navigation: {nextEl: ".card-slider__next", prevEl: ".card-slider__prev"},
        breakpoints: {
            1366: {slidesPerView: 4, spaceBetween: 18},
            780: {slidesPerView: 3, spaceBetween: 18},
            480: {slidesPerView: 2, spaceBetween: 18}
        }
    }), new Swiper(".services-detail-slider__container", {
        speed: 2e3,
        grabCursor: !0,
        slidesPerView: 1,
        spaceBetween: 20,
        navigation: {nextEl: ".services-detail__next", prevEl: ".services-detail__prev"},
        breakpoints: {
            1681: {slidesPerView: 3, spaceBetween: 131},
            981: {slidesPerView: 3, spaceBetween: 40},
            601: {slidesPerView: 2, spaceBetween: 20}
        }
    })
}, function (e, t) {
    var i = {
        close: function () {
            $(".popup").fadeOut().removeClass("js-isShowed"), bodyScrollLock.clearAllBodyScrollLocks()
        }, open: function (e) {
            bodyScrollLock.disableBodyScroll(e.find(".popup__body"), {
                allowTouchMove: function (e) {
                    for (; e && e !== document.body;) {
                        if (null !== e.getAttribute("data-body-scroll-lock-ignore")) return !0;
                        e = e.parentNode
                    }
                }
            }), e.fadeToggle().toggleClass("js-isShowed")
        }, toggle: function (e) {
            var t = $(e);
            t.hasClass("js-isShowed") ? this.close() : this.open(t)
        }
    };
    $((function () {
        $("[data-modal]").click((function () {
            $("." + $(this).data("modal")).length && (i.close(), i.toggle("." + $(this).data("modal")))
        })), $(".js-closePopup").click((function () {
            i.close()
        }));
        $(".js-watchPopup").click((function (e) {
            !function (e) {
                var t = e.target;
                $(".js-isShowed").length > 0 && t !== document.querySelector(".popup__body") && t === document.querySelector(".js-isShowed").querySelector(".popup__overlay") && i.close()
            }(e)
        }))
    }))
}, function (e, t) {
    window.sizeCounter = function (e) {

    }
}, function (e, t) {
    window.initFilterInputs = function () {
        var e = $(".js-rangeSliderMinimal"), t = $(".js-rangeSliderMaximal"), i = t.data("max"), n = e.data("min");
        $(".catalog-filter-range__input").change((function () {
            var o = e.val(), a = t.val();
            parseInt(o) > parseInt(a) && (o = a), a > i && (a = i), parseInt(o) < parseInt(n) && (o = n), e.val(o), t.val(a)
        }))
    }, window.initTweenAnimations = function () {
        $(".catalog-filter-item_open-always").length > 0 && $(".catalog-filter-item_open-always").each((function () {
            $(this).find(".catalog-filter-item__content").css("height", "auto")
        })), $(".catalog-filter-item__title").each((function () {
            var e = $(this).parent(), t = $(this).siblings(".catalog-filter-item__content").get(0),
                i = new TimelineLite({id: "tweenToggle"});
            i.to(t, .6, {height: t.scrollHeight}), i.to(t, .3, {height: "auto"}), i.pause(), $(this).data("tweenToggle", i), e.hasClass("catalog-filter-item_open") && (e.addClass("catalog-filter-item_open"), $(this).data("tweenToggle").play()), $(this).click((function () {
                e.hasClass("catalog-filter-item_open") ? (e.removeClass("catalog-filter-item_open"), $(this).data("tweenToggle").reverse()) : (e.addClass("catalog-filter-item_open"), $(this).data("tweenToggle").play())
            }))
        }))
    }, document.addEventListener("DOMContentLoaded", (function () {
        $(window).width() < 1025 && ($(".catalog-filter-wrap__remove").appendTo(".catalog-aside-block__drop"), $(".catalog-section-top-items").appendTo(".catalog-aside-sort-mobile"), $(".catalog-aside-block__title").click((function () {
            var e = $(this).parent().siblings(".catalog-aside-block__drop");
            if (e.is(":animated")) return !1;
            $(this).hasClass("catalog-aside-block__title_open") ? ($(this).removeClass("catalog-aside-block__title_open"), e.removeClass(".is-open")) : ($(this).addClass("catalog-aside-block__title_open"), e.addClass("is-open"))
        })), $(".catalog-aside-block__drop-close").click((function () {
            $(".catalog-aside-block__drop").removeClass("is-open"), $(".catalog-aside-block__title").removeClass("catalog-aside-block__title_open")
        })), $(".catalog-aside-sort-mobile__button").click((function () {
            $(this).siblings(".catalog-section-top-items").toggleClass("is-open")
        })))
    })), window.checkCounters = function (e) {
        return e.find('input[type="checkbox"]:checked').length
    }, window.initCounters = function () {
        $(".js-counter").each((function () {
            var e = $(this).find(".catalog-filter-item__count"), t = $(this);
            $(this).find('input[type="checkbox"]').change((function () {
                e.text("(".concat(window.checkCounters(t), ")"))
            }))
        }))
    }, $((function () {
        window.initFilterInputs(), window.initTweenAnimations(), window.initCounters()
    }))
}, function (e, t, i) {
    "use strict";
    i.r(t);
    var n = i(0), o = i.n(n);
    i(1), i(2), i(3);

    function a(e) {
        if ("undefined" == typeof Symbol || null == e[Symbol.iterator]) {
            if (Array.isArray(e) || (e = function (e, t) {
                if (!e) return;
                if ("string" == typeof e) return r(e, t);
                var i = Object.prototype.toString.call(e).slice(8, -1);
                "Object" === i && e.constructor && (i = e.constructor.name);
                if ("Map" === i || "Set" === i) return Array.from(i);
                if ("Arguments" === i || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(i)) return r(e, t)
            }(e))) {
                var t = 0, i = function () {
                };
                return {
                    s: i, n: function () {
                        return t >= e.length ? {done: !0} : {done: !1, value: e[t++]}
                    }, e: function (e) {
                        throw e
                    }, f: i
                }
            }
            throw new TypeError("Invalid attempt to iterate non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")
        }
        var n, o, a = !0, s = !1;
        return {
            s: function () {
                n = e[Symbol.iterator]()
            }, n: function () {
                var e = n.next();
                return a = e.done, e
            }, e: function (e) {
                s = !0, o = e
            }, f: function () {
                try {
                    a || null == n.return || n.return()
                } finally {
                    if (s) throw o
                }
            }
        }
    }

    function r(e, t) {
        (null == t || t > e.length) && (t = e.length);
        for (var i = 0, n = new Array(t); i < t; i++) n[i] = e[i];
        return n
    }

    function s(e, t) {
        if (!(e instanceof t)) throw new TypeError("Cannot call a class as a function")
    }

    function l(e, t) {
        for (var i = 0; i < t.length; i++) {
            var n = t[i];
            n.enumerable = n.enumerable || !1, n.configurable = !0, "value" in n && (n.writable = !0), Object.defineProperty(e, n.key, n)
        }
    }

    function c(e, t, i) {
        return t && l(e.prototype, t), i && l(e, i), e
    }

    var u = function () {
        function e(t, i, n) {
            s(this, e), this.tabList = t, this.toggle = i, this.tab = n, this.init()
        }

        return c(e, [{
            key: "init", value: function () {
                var e = this;
                this.toggle.classList.contains("tabs__toggle_active") ? this.open() : this.close(), this.toggle.addEventListener("click", (function () {
                    e.open()
                }))
            }
        }, {
            key: "open", value: function () {
                this.tabList.active !== this && (this.tabList.active && this.tabList.active.close(), this.tabList.active = this, this.tab.style.display = "flex", this.toggle.classList.add("tabs__toggle_active"))
            }
        }, {
            key: "close", value: function () {
                this.tab.style.display = "none", this.toggle.classList.remove("tabs__toggle_active")
            }
        }]), e
    }(), d = function () {
        function e(t) {
            s(this, e), this.container = t, this.init()
        }

        return c(e, [{
            key: "init", value: function () {
                var e = this;
                this.toggleList = this.container.querySelectorAll(".tabs__toggle"), this.tabList = this.container.querySelectorAll(".tabs__tab"), this.checkValid() && this.toggleList.forEach((function (t, i) {
                    new u(e, t, e.tabList[i])
                }))
            }
        }, {
            key: "checkValid", value: function () {
                return this.toggleList.length === this.tabList.length && (0 !== this.toggleList.length && 0 !== this.tabList.length)
            }
        }]), e
    }();
    i(4);
    var p = {
        $burger: $(".header-burger"), $menu: $(".header-mobile"), opened: !1, open: function () {
            this.opened || (this.$burger.addClass("header-burger_open"), this.$menu.addClass("header-mobile_open"), this.opened = !0)
        }, close: function () {
            this.opened && (this.$burger.removeClass("header-burger_open"), this.$menu.removeClass("header-mobile_open"), this.opened = !1)
        }, toggle: function () {
            this.opened ? this.close() : this.open()
        }, handleClose: function (e) {
            var t = e.target;
            console.log(e), t === document.querySelector(".header-mobile") && this.opened && this.close()
        }, init: function () {
            var e = this;
            this.$burger.click((function () {
                e.toggle()
            })), this.$menu.click((function (t) {
                e.handleClose(t)
            })), $(".header-mobile__list li").click((function (e) {
                if ($(window).width() <= 1024 && ($(this).find("ul").length > 0 && !$(this).hasClass("is-open"))) return $(this).addClass("is-open"), e.preventDefault(), !1
            })), $(document).keyup((function (t) {
                "Escape" === t.key && e.close()
            }))
        }
    };
    document.addEventListener("DOMContentLoaded", (function () {
        document.querySelector(".card-tabs__wrapper") && (!function (e) {
            var t, i = a(document.querySelectorAll(e));
            try {
                for (i.s(); !(t = i.n()).done;) {
                    var n = t.value;
                    new d(n)
                }
            } catch (e) {
                i.e(e)
            } finally {
                i.f()
            }
        }(".card-tabs__wrapper"), console.log(123))
    })), o()((function () {
        p.init(), o()('[data-fancybox="gallery"]').fancybox({})
    }))
}]);
//# sourceMappingURL=app.js.map