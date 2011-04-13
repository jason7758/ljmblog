/* mod lazyload @author kangsai */
(function(l) {
    l.lazyload = {};
    l.lazyload.defaults = {
        container: window,
        placeholder: "",
        defined_src: "lazy-load-src",
        threshold: 0,
        direction: 1,
        delay: 100,
        lock: false,
        timer: null,
        set: null
    };
    l.fn.lazyload = function(a) {
        var a = l.extend(l.lazyload.defaults, a);
        a.elements = l(this);
        a.lastScroll = 0;
        a.threshold = j(a);
        n(a);
        a.set = function() {
            i(a)
        };
        l(a.container).bind("scroll", a.set);
        l(a.container).bind("resize", a.set)
    };
    function n(f) {
        var b = o(f);
        var a = f.lastScroll;
        var g = b;
        f.lastScroll = b;
        var e = m(a, g);
        var p = l(window).height();
        if (e == 1) {
            if (g - a > p) {
                a = g - p
            }
            a -= f.threshold;
            g += f.threshold;
            h(f, a, g)
        } else {
            if (e == 2) {
                if (a - g > p) {
                    a = g + p
                }
                a += f.threshold;
                g = g - p - f.threshold;
                h(f, g, a)
            }
        }
    }
    function h(b, a, e) {
        if (b.elements.length == 0) {
            l(b.container).unbind("scroll", b.set);
            l(b.container).unbind("resize", b.set);
            return false
        }
        b.elements.each(function(q) {
            var f = l(this);
            var g = f.offset().top;
            var p = g + f.height();
            if ((g >= a && g <= e) || (p >= a && p <= e)) {
                if (f.attr(b.defined_src)) {
                    f.attr("src", f.attr(b.defined_src));
                    f.removeAttr(b.defined_src)
                }
            } else {
                if (f.attr(b.defined_src) && b.placeholder) {
                    f.attr("src", b.placeholder)
                }
            }
        });
        b.elements = l("img[" + b.defined_src + "]")
    }
    function o(a) {
        return l(window).height() + l(window).scrollTop()
    }
    function m(a, e) {
        var b = 1;
        if (a - e > 0) {
            b = 2
        } else {
            if (a - e < 0) {
                b = 1
            }
        }
        return b
    }
    function i(a) {
        clearTimeout(a.timer);
        if (a.lock) {
            a.timer = setTimeout(function() {
                i(a)
            },
            a.delay)
        } else {
            a.lock = true;
            n(a);
            setTimeout(function() {
                a.lock = false
            },
            a.delay)
        }
    }
    function j(a) {
        if (a.threshold > 100) {
            a.threshold = 100
        }
        return a.threshold * l(window).height() / 100
    }
})(jQuery);