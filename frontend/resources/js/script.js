var //var go to top button
    scrollTrigger = 100, // px
    backToTop = function (gototop) {
        var scrollTop = $(window).scrollTop();
        if (scrollTop > scrollTrigger) {
            $(gototop).addClass('show');
        } else {
            $(gototop).removeClass('show');
        }
    },

    resizeCarousel = function () {
        //set backend carousel fullHeight
        $windowHeight = $(window).height() - $('.nav-container').outerHeight();
        $('.uk-slideshow-items').height($windowHeight);
        $('.uk-slideshow-items .el-item').height($windowHeight);
    };


$(document).ready(function () {
    
    resizeCarousel();
    $('.content-bottom-megamenu-slim.collapse').collapse('show');
    if ($(document).width() < 992) {
        $('.content-bottom-megamenu-slim.collapse').collapse('hide');
    } 
    var footerHeight = $("#megamenu-slim-eventi").outerHeight();
    var topFooterHeight = $(".content-top-megamenu").outerHeight();
    $("html").css('padding-bottom', footerHeight);
    if ($(document).width() < 992) { 
        $("html").css('padding-bottom', topFooterHeight);
    }

    /* PLAYLIST YOUTUBE */
    $('.wrap-playlist .wrap-video-thumbs .wrap-video').click(function () {
        var videoId = $(this).find('img').data('video');
        $('.wrap-active-video iframe').attr('src', 'https://www.youtube.com/embed/' + videoId);
        $('.wrap-playlist .wrap-video-thumbs .wrap-video').removeClass('active');
        $(this).addClass('active');
    });


    //fix slider IE
    var userAgent, ieReg, ie;
    userAgent = window.navigator.userAgent;
    ieReg = /msie|Trident.*rv[ :]*11\./gi;
    ie = ieReg.test(userAgent);

    if (ie) {
        $(".lSliderItem, .content-image, .panel-link-item").each(function () {
            var $container = $(this),
                imgUrl = $container.find("img").prop("src");
            if (imgUrl) {
                $container.css("backgroundImage", 'url(' + imgUrl + ')').addClass("custom-object-fit");
            }
        });
    }
    //END: fix slider IE
});

$(window).resize(function () {
    resizeCarousel();
});

// PLAY E PAUSA VIDEO

function playVid() {
    var vid = document.getElementsByClassName("video-bg")[0].getElementsByTagName('video')[0];
    console.log(vid);
    vid.play();
}

function pauseVid() {
    var vid = document.getElementsByClassName("video-bg")[0].getElementsByTagName('video')[0];
    console.log(vid);
    vid.pause();
}

$(document).ready(function () {

    $('a.cta-intro').click(function () {
        $('html, body').animate({
            scrollTop: $("div.text-intro").offset().top - 70
        }, 1000)
    }),
        $('a.cta-programma').click(function () {
            $('html, body').animate({
                scrollTop: $("div.section-programma").offset().top - 70
            }, 1000)
        }),
        $('a.cta-protagonisti').click(function () {
            $('html, body').animate({
                scrollTop: $("div.section-protagonisti").offset().top - 70
            }, 1000)
        }),
        $('a.cta-news').click(function () {
            $('html, body').animate({
                scrollTop: $("div.section-news").offset().top - 70
            }, 1000)
        }),
        $('a.cta-gallery').click(function () {
            $('html, body').animate({
                scrollTop: $("div.section-gallery").offset().top - 70
            }, 1000)
        }),
        $('a.cta-video').click(function () {
            $('html, body').animate({
                scrollTop: $("div.section-video").offset().top - 70
            }, 1000)
        })
    $('a.cta-scopri-sala').click(function () {
        $('html, body').animate({
            scrollTop: $("div.second-section").offset().top - 70
        }, 1000)
    }),
        $('a.cta-scopri-gallery-sala').click(function () {
            $('html, body').animate({
                scrollTop: $("div.section-gallery").offset().top - 70
            }, 1000)
        })

});

$(window).load(function () {
    $('.js-custom-lightbox > div > .uk-slider-items').attr('uk-lightbox', '');
    $('.gallery-opere > .uk-first-column > a').attr('href', 'javascript:void(0)');
    $('.button-next-section').attr('href', 'javascript:void(0)');
    
    $('.link-footer-piano > .el-content > .el-title ').remove();
    
    // var p = document.querySelector(".luogo-evento p");
    // var testo = $('.luogo-evento p').text();
    // var H = document.createElement("h2");
    // p.replaceWith(H);
    // $('.luogo-evento h2').text(testo);
});

$(function() {
    $('[data-toggle="popover"]').popover()
})

$('.popover-dismiss').popover({
    trigger: 'focus'
})