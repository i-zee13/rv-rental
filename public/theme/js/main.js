(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner(0);
    
    
    // Initiate the wowjs
    new WOW().init();


    // Sticky Navbar — match original template scroll behavior
    function updateNavbar() {
        var $nav = $('.nav-bar');
        var topbarVisible = window.matchMedia('(min-width: 1200px)').matches;
        if ($(window).scrollTop() > 5) {
            $nav.addClass('scrolled shadow-sm');
            $nav.css('top', '0px');
        } else {
            $nav.removeClass('scrolled shadow-sm');
            $nav.css('top', topbarVisible ? '45px' : '0px');
        }
    }
    updateNavbar();
    $(window).on('scroll resize', updateNavbar);


    // Car Categories
    $(".categories-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        dots: false,
        loop: true,
        margin: 25,
        nav : true,
        navText : [
            '<i class="fas fa-chevron-left"></i>',
            '<i class="fas fa-chevron-right"></i>'
        ],
        responsiveClass: true,
        responsive: {
            0:{
                items:1
            },
            576:{
                items:1
            },
            768:{
                items:1
            },
            992:{
                items:2
            },
            1200:{
                items:3
            }
        }
    });


    // Homepage compact card scrollers (drag cards + drag scrollbar, auto-move)
    function initHomeCardAutoscroll() {
        document.querySelectorAll('[data-home-autoscroll]').forEach(function (track) {
            var scroller = track.closest('.home-cards-scroller');
            var thumb = scroller ? scroller.querySelector('.home-cards-scrollthumb') : null;
            var trackBar = scroller ? scroller.querySelector('.home-cards-scrolltrack') : null;
            var paused = false;
            var userInteracting = false;
            var resumeTimer = null;
            var trackDragging = false;
            var thumbDragging = false;
            var dragStartX = 0;
            var dragStartScroll = 0;
            var dragMoved = false;

            function maxScroll() {
                return Math.max(0, track.scrollWidth - track.clientWidth);
            }

            function updateThumb() {
                if (!thumb || !trackBar) return;
                var max = maxScroll();
                if (max <= 0) {
                    trackBar.classList.add('is-hidden');
                    return;
                }
                trackBar.classList.remove('is-hidden');
                var ratio = track.clientWidth / track.scrollWidth;
                var thumbWidth = Math.max(12, ratio * 100);
                var left = (track.scrollLeft / max) * (100 - thumbWidth);
                thumb.style.width = thumbWidth + '%';
                thumb.style.left = left + '%';
            }

            function pauseBriefly(ms) {
                userInteracting = true;
                clearTimeout(resumeTimer);
                resumeTimer = setTimeout(function () {
                    userInteracting = false;
                }, ms || 5000);
            }

            function setDraggingState(active) {
                track.classList.toggle('is-dragging', active);
                if (thumb) thumb.classList.toggle('is-dragging', active);
            }

            function scrollFromBarX(clientX, centerThumb) {
                var max = maxScroll();
                if (!trackBar || max <= 0) return;
                var rect = trackBar.getBoundingClientRect();
                var thumbRect = thumb ? thumb.getBoundingClientRect() : { width: rect.width * 0.2 };
                var travel = rect.width - thumbRect.width;
                var offset = centerThumb ? thumbRect.width / 2 : 0;
                var pct = Math.max(0, Math.min(1, (clientX - rect.left - offset) / Math.max(1, travel)));
                track.scrollLeft = pct * max;
            }

            // Drag cards horizontally (mouse + touch)
            track.addEventListener('pointerdown', function (e) {
                if (e.pointerType === 'mouse' && e.button !== 0) return;
                trackDragging = true;
                dragMoved = false;
                dragStartX = e.clientX;
                dragStartScroll = track.scrollLeft;
                setDraggingState(true);
                pauseBriefly();
                try { track.setPointerCapture(e.pointerId); } catch (_) {}
            });

            track.addEventListener('pointermove', function (e) {
                if (!trackDragging) return;
                var dx = e.clientX - dragStartX;
                if (Math.abs(dx) > 4) dragMoved = true;
                track.scrollLeft = dragStartScroll - dx;
            });

            function endTrackDrag(e) {
                if (!trackDragging) return;
                trackDragging = false;
                setDraggingState(false);
                try { track.releasePointerCapture(e.pointerId); } catch (_) {}
                if (dragMoved) {
                    track.dataset.suppressClick = '1';
                    setTimeout(function () { delete track.dataset.suppressClick; }, 350);
                }
                updateThumb();
            }

            track.addEventListener('pointerup', endTrackDrag);
            track.addEventListener('pointercancel', endTrackDrag);

            track.addEventListener('click', function (e) {
                if (track.dataset.suppressClick) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, true);

            track.addEventListener('scroll', updateThumb, { passive: true });
            window.addEventListener('resize', updateThumb);
            track.addEventListener('mouseenter', function () { paused = true; });
            track.addEventListener('mouseleave', function () {
                if (!trackDragging && !thumbDragging) paused = false;
            });
            track.addEventListener('wheel', function () { pauseBriefly(); }, { passive: true });

            // Click track bar to jump
            if (trackBar) {
                trackBar.addEventListener('click', function (e) {
                    if (thumbDragging || dragMoved) return;
                    if (e.target === thumb) return;
                    scrollFromBarX(e.clientX, true);
                    pauseBriefly();
                    updateThumb();
                });
            }

            // Drag scrollbar thumb
            if (thumb && trackBar) {
                thumb.addEventListener('pointerdown', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    thumbDragging = true;
                    dragStartX = e.clientX;
                    dragStartScroll = track.scrollLeft;
                    setDraggingState(true);
                    pauseBriefly(8000);
                    try { thumb.setPointerCapture(e.pointerId); } catch (_) {}
                });

                thumb.addEventListener('pointermove', function (e) {
                    if (!thumbDragging) return;
                    var max = maxScroll();
                    var rect = trackBar.getBoundingClientRect();
                    var thumbRect = thumb.getBoundingClientRect();
                    var travel = Math.max(1, rect.width - thumbRect.width);
                    var dx = e.clientX - dragStartX;
                    var ratio = max / travel;
                    track.scrollLeft = Math.max(0, Math.min(max, dragStartScroll + dx * ratio));
                });

                function endThumbDrag(e) {
                    if (!thumbDragging) return;
                    thumbDragging = false;
                    setDraggingState(false);
                    try { thumb.releasePointerCapture(e.pointerId); } catch (_) {}
                    updateThumb();
                }

                thumb.addEventListener('pointerup', endThumbDrag);
                thumb.addEventListener('pointercancel', endThumbDrag);
            }

            updateThumb();

            setInterval(function () {
                if (paused || userInteracting || trackDragging || thumbDragging) return;
                var max = maxScroll();
                if (max <= 0) return;
                var step = Math.max(220, track.clientWidth * 0.72);
                if (track.scrollLeft >= max - 8) {
                    track.scrollTo({ left: 0, behavior: 'smooth' });
                } else {
                    track.scrollBy({ left: step, behavior: 'smooth' });
                }
            }, 3800);
        });
    }

    initHomeCardAutoscroll();


    // testimonial carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        center: false,
        dots: true,
        loop: true,
        margin: 25,
        nav : false,
        navText : [
            '<i class="fa fa-angle-right"></i>',
            '<i class="fa fa-angle-left"></i>'
        ],
        responsiveClass: true,
        responsive: {
            0:{
                items:1
            },
            576:{
                items:1
            },
            768:{
                items:1
            },
            992:{
                items:2
            },
            1200:{
                items:2
            }
        }
    });


    // Facts counter
    $('[data-toggle="counter-up"]').counterUp({
        delay: 5,
        time: 2000
    });


   // Back to top button
   $(window).scroll(function () {
    if ($(this).scrollTop() > 300) {
        $('.back-to-top').fadeIn('slow');
    } else {
        $('.back-to-top').fadeOut('slow');
    }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


})(jQuery);

