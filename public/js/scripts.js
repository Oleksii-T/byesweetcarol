document.addEventListener("DOMContentLoaded", (function () {
    function e(e) {
        let t = e.querySelector(".video__link"),
            s = e.querySelector(".video__media"),
            n = e.querySelector(".video__button"),
            o = function (e) {
                let t = /https:\/\/i\.ytimg\.com\/vi\/([a-zA-Z0-9_-]+)\/maxresdefault\.jpg/i,
                    s = e.src;
                return s.match(t)[1]
            }(s);
        e.addEventListener("click", (() => {
            let s = function (e) {
                let t = document.createElement("iframe");
                return t.setAttribute("allowfullscreen", ""), t.setAttribute("allow", "autoplay"), t.setAttribute("src", function (e) {
                    return "https://www.youtube.com/embed/" + e + "?rel=0&showinfo=0&autoplay=1"
                }(e)), t.classList.add("video__media"), t
            }(o);
            t.remove(), n.remove(), e.appendChild(s)
        })), t.removeAttribute("href"), e.classList.add("video--enabled")
    } ! function () {
        let t = document.querySelectorAll(".video");
        for (let s = 0; s < t.length; s++) e(t[s])
    }();
    const t = document.querySelector(".js-menu"),
        s = document.querySelector(".header__menu"),
        n = document.querySelector("body");
    t.addEventListener("click", (() => {
        s.classList.toggle("active"), t.classList.toggle("active"), n.classList.toggle("active")
    }));
    const o = document.querySelector(".header__search-button"),
        i = document.querySelector(".header__search"),
        c = document.querySelector(".header__search-closeBtn");
    o.addEventListener("click", (() => {
        i.classList.add("active")
    })), c.addEventListener("click", (() => {
        i.classList.remove("active")
    })), window.onclick = function (e) {
        e.target == i && i.classList.remove("active")
    };

    const anchorLinks = document.querySelectorAll(".anchor-link");
    for (const anchor of anchorLinks) {
        anchor.addEventListener("click", smoothScroll);
    }

    function smoothScroll(event) {
        event.preventDefault();
        const hrefParts = this.getAttribute("href").split("#");
        console.log(`document.getElementById(hrefParts[1]).getBoundingClientRect().top`, document.getElementById(hrefParts[1]).getBoundingClientRect().top); //! LOG
        console.log(`document.documentElement.scrollTop`, document.documentElement.scrollTop); //! LOG
        const targetPosition = document.getElementById(hrefParts[1]).getBoundingClientRect().top + document.documentElement.scrollTop - 20;
        console.log(`targetPosition`, targetPosition); //! LOG
        scroll({
            top: targetPosition,
            behavior: "smooth"
        });
    }

    const a = document.querySelectorAll(".links__menu-button"),
        d = document.querySelectorAll(".links__submenu-item"),
        u = document.querySelector(".links__submenu"),
        m = document.querySelector(".links__menu"),
        v = document.querySelectorAll(".links__submenu-button");
    a.forEach((e => {
        e.addEventListener("click", (function () {
            const e = this.getAttribute("data-target"),
                t = document.getElementById(e);
            u.classList.add("opened"), d.forEach((t => {
                t.id !== e && t.classList.remove("open")
            })), t.classList.toggle("open"), m.classList.toggle("opened")
        }))
    })), v.forEach((e => {
        e.addEventListener("click", (function () {
            this.closest(".links__submenu").classList.remove("open"), m.classList.remove("opened"), u.classList.remove("opened"), setTimeout, d.forEach((e => {
                setTimeout((function () {
                    e.classList.remove("open")
                }), 200)
            }))
        }))
    }));
    const p = document.querySelector(".links__button-toggle"),
        _ = document.querySelector(".links__wrapper"),
        h = document.querySelector(".links__button-close");
    p && p.addEventListener("click", (() => {
        _.classList.add("active"), n.classList.add("active")
    })), h && h.addEventListener("click", (() => {
        _.classList.remove("active"), n.classList.remove("active")
    })), expanderList = document.querySelectorAll(".js-button-expander"), $(".js-button-expander").click((function (e) {
        for (var t = $(this).parent(), s = $(t).children(".js-expand-content"), n = 0; n < expanderList.length; n++)
            if (expanderList[n] == this)
                for (var o = 0; o < s.length; o++) $(s[o]).hasClass("expanded") ? ($(t).removeClass("active"), $(s[o]).removeClass("expanded"), $(s[o]).slideUp()) : ($(t).addClass("active"), $(s[o]).addClass("expanded"), $(s[o]).slideDown());
            else {
                var i = $(expanderList[n]).parent(),
                    c = $(i).children(".js-expand-content");
                $(c).hasClass("expanded") && $(i).hasClass("active") && ($(i).removeClass("active"), $(c).removeClass("expanded"), $(c).slideUp())
            }
    })), $(window).width() <= 1140 && ($(".sidebar__nav-button").click((function () {
        $(".sidebar__nav-list").slideToggle(500), $(this).toggleClass("active")
    })), $(".sidebar__details-button").click((function () {
        $(".sidebar__details-list").slideToggle(500), $(this).toggleClass("active")
    }))), $(".screens-slider").length && $(".screens-slider").slick({
        infinite: !0,
        lazyLoad: "ondemand",
        slidesToShow: 3,
        slidesToScroll: 1,
        dots: !0,
        speed: 800,
        responsive: [{
            breakpoint: 799,
            settings: {
                slidesToShow: 2
            }
        }, {
            breakpoint: 600,
            settings: {
                slidesToShow: 1
            }
        }]
    })
}));
