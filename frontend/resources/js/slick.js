
// SLICK SLIDER
// SCROLL TO CON CLASSI
function scrollNumbers(currentSlider) {

    $('.slick-current.slick-active .numero-piano').each(function () {
        var piano_slider = $(this).text();
        console.log(piano_slider);
        piano_slider = parseInt(piano_slider, 10);
        if (piano_slider >= 0) {
            $(this).prop('Counter', currentSlider - 1).animate({
                Counter: piano_slider
            }, {
                duration: 2000,
                easing: 'swing',
                step: function (now) {
                    $(this).text(Math.ceil(now));
                }
            });
        } else {
            /** piani < 0 */
            var positive_piano = $(this).attr('data-title') * (-1);
            $(this).prop('Counter', -1).animate({

                Counter: positive_piano
            }, {
                duration: 2000,
                easing: 'swing',
                step: function (now) {
                    $(this).text(Math.ceil(now) * (-1));
                }
            });
        }
    });

}


$(document).ready(function () {

    var currentSlide = $('div.slick-current .numero-piano').text();
    currentSlide = parseInt(currentSlide, 10);

    $('.actions-piani li').click(function () {
        var nth_child = $(this).parent().children().length - $(this).nextAll().length - 1;
        var piano_li = $(this).text().substring(6);
        piano_li = parseInt(piano_li, 10);

        $('div[data-slick-index=' + nth_child + '] .numero-piano').each(function () {
            var piano_slider = $(this).text();
            piano_slider = parseInt(piano_slider, 10);


            $('.actions-piani li ').css("pointer-events", "none");
            if (piano_li != currentSlide) {
                console.log(currentSlide);
                console.log(piano_li);
                if (piano_slider >= 0) {
                    $(this).prop('Counter', currentSlide - 1).animate({
                        Counter: piano_slider
                    }, {
                        duration: 2000,
                        easing: 'swing',
                        step: function (now) {
                            $(this).text(Math.ceil(now));
                        }
                    });
                } else {
                    /** piani < 0 */
                    var positive_piano = $(this).attr('data-title') * (-1);
                    $(this).prop('Counter', -1).animate({

                        Counter: positive_piano
                    }, {
                        duration: 3000,
                        easing: 'swing',
                        step: function (now) {
                            $(this).text(Math.ceil(now) * (-1));
                        }
                    });
                }

            }

            $('.actions-piani li ').css("pointer-events", "auto");
            currentSlide = piano_slider;

        });

    });

    // $('button.arrow-up').click(function () {
    //     scrollNumbers(0);
    // });
    // $('button.arrow-down').click(function () {
    //    scrollNumbers(40);
    // });
    var floor = window.location.hash.substr(7);
    var plSlide = jQuery('.slider-pl').find('.numero-piano[data-title="' + floor + '"]').parents('.piano-palazzo').index();
    var gpSlide = jQuery('.slider-gp').find('.numero-piano[data-title="' + floor + '"]').parents('.piano-palazzo').index();
    // var plFirstFloor = jQuery('.slider-pl').find('.numero-piano').length - 1;
    // var gpFirstFloor = jQuery('.slider-gp').find('.numero-piano').length - 1;

    // SLIDER PALAZZO LOMBARDIA
    //Implementing navigation of slides using mouse scroll
    const $sliderPl = $(".slider-pl");


    $sliderPl
        .on('init', () => {
            mouseWheel($sliderPl);
            // $('.content-bottom-megamenu.collapse').collapse('show');
            if ($(document).width() < 992) {
                $('.content-bottom-megamenu.collapse').collapse('hide');
            } else {
                $('.content-bottom-megamenu.collapse').collapse('show');
            }
        })

        .slick({
            dots: true,
            vertical: true,
            infinite: false,
            edgeFriction: 10,
            slidesToShow: 1,
            slidesToScroll: 1,
            adaptiveHeight: true,
            accessibility: true,
            arrows: true,
            initialSlide: plSlide >= 0 ? plSlide : 7,

            respondTo: 'slider',
            speed: 1000,
            focusOnSelect: true,
            dotsClass: 'actions-piani',
            touchThreshold: 10,
            verticalSwiping: true,
            onAfterChange: afterChange,
            customPaging: function (slider, i) {
                var title = $(slider.$slides[i].innerHTML).find('.numero-piano[data-title]').data('title');
                return '<a href="javascript:void(0)" class="pager__item"><span>PIANO</span> ' + title + ' </a>';
            },

            prevArrow: '<button type="button" aria-label="Prev" class="arrow-up"><span class="mdi mdi-chevron-up-circle mdi-24px"></span><span>Scorri verso l\'alto</span></button>',
            nextArrow: '<button type="button" aria-label="Next" class="arrow-down"><span class="mdi mdi-chevron-down-circle mdi-24px"></span><span>Scorri verso il basso</span></button>',


        });
    $('.slider-pl').on('afterChange', function (event, slick, currentSlide) {

        if (currentSlide !== 7) {
            $('.content-bottom-megamenu.collapse').collapse('hide');
        }
    //     if (currentSlide !== 7) {
    //         $('.content-bottom-megamenu.collapse').collapse('hide');

    //     }
    //     // else {
    //     //     $('.content-bottom-megamenu.collapse').collapse('show');

    });
    // $(window).resize(function(){
    //     event.preventDefault();
    //     var $windowWidth = $(window).width();
    //     if ($windowWidth < 1024) {
    //         // slickify();  

    //         $(window).resize(function() {
    //             $('.slider-pl').slick('resize');
    //             $('.content-bottom-megamenu.collapse').collapse('hide');
    //           });

    //           $(window).on('orientationchange', function() {
    //             $('.slider-pl').slick('resize');
    //             $('.content-bottom-megamenu.collapse').collapse('hide');
    //           });
    //     }
    // });

    function mouseWheel($sliderPl) {
        $(window).on('wheel', { slider: $sliderPl }, mouseWheelHandler)
    }

    // SLIDER GRATTACIELO PIRELLI
    //Implementing navigation of slides using mouse scroll
    const $sliderGp = $(".slider-gp");
    $sliderGp
        .on('init', () => {
            mouseWheel($sliderGp);
            // $('.content-bottom-megamenu.collapse').collapse('show');
            if ($(document).width() < 992) {
                $('.content-bottom-megamenu.collapse').collapse('hide');
            } else {
                $('.content-bottom-megamenu.collapse').collapse('show');
            }
        })
        .slick({
            dots: true,
            vertical: true,
            infinite: false,
            edgeFriction: 10,
            slidesToShow: 1,
            slidesToScroll: 1,
            adaptiveHeight: true,
            accessibility: true,
            arrows: true,
            initialSlide: gpSlide >= 0 ? gpSlide : 5,

            respondTo: 'slider',
            speed: 1000,
            focusOnSelect: true,
            dotsClass: 'actions-piani',
            onAfterChange: afterChange,
            touchThreshold: 10,
            verticalSwiping: true,
            customPaging: function (slider, i) {
                var title = $(slider.$slides[i].innerHTML).find('span[data-title]').data('title');
                return '<a href="javascript:void(0)" class="pager__item"><span>PIANO</span> ' + title + ' </a>';
            },

            prevArrow: '<button type="button" aria-label="Prev" class="arrow-up"><span class="mdi mdi-chevron-up-circle mdi-24px"></span><span>Scorri verso l\'alto</span></button>',
            nextArrow: '<button type="button" aria-label="Next" class="arrow-down"><span class="mdi mdi-chevron-down-circle mdi-24px"></span><span>Scorri verso il basso</span></button>',


        });

    $('.slider-gp').on('afterChange', function (event, slick, currentSlide) {

        if (currentSlide !== 5) {
            $('.content-bottom-megamenu.collapse').collapse('hide');
            // $('.btn-megamenu').attr('aria-expanded', 'true');
        }
        // else {
        //     $('.content-bottom-megamenu.collapse').collapse('hide');
        //     // $('.btn-megamenu').attr('aria-expanded', 'false');
        // }

    })

});
function mouseWheel($sliderGp) {
    $(window).on('wheel', { slider: $sliderGp }, mouseWheelHandler)
}
function mouseWheelHandler(event) {
    //event.preventDefault()
    var slider = event.data.slider
    const delta = event.originalEvent.deltaY

    if(typeof slider.isScrolling == "undefined" || slider.isScrolling == false) {
        slider.isScrolling = true;
        setTimeout(function() {
            slider.isScrolling = false;
            }, 1500);

        if (delta < 0) {
            slider.slick('slickPrev');
        } else {
            slider.slick('slickNext');
        }
    }
}

// SLIDER SCROLL
const $sliderScroll = $("#slider-scroll");

$sliderScroll
    .on('init', () => {
        mouseWheel($sliderScroll)
        $('.content-bottom-megamenu.collapse').collapse('show');

    })
    .slick({
        dots: true,
        vertical: true,
        infinite: false,
        edgeFriction: 20,
        slidesToShow: 1,
        slidesToScroll: 1,
        adaptiveHeight: true,
        accessibility: true,
        arrows: true,
        verticalSwiping: false,
        initialSlide: 7,
        respondTo: 'slider',
        speed: 1000,
        focusOnSelect: true,
        dotsClass: 'actions-piani',
        onAfterChange: afterChange,
        touchThreshold: 20,
        verticalSwiping: true,
        customPaging: function (slider, i) {
            var title = $(slider.$slides[i].innerHTML).find('.numero-piano[data-title]').data('title');
            return '<a href="javascript:void(0)" class="pager__item"><span>PIANO</span> ' + title + ' </a>';
        },
        prevArrow: '<button type="button" aria-label="Prev" class="arrow-up"><span class="mdi mdi-chevron-up-circle mdi-24px"></span><span>Scorri verso l\'alto</span></button>',
        nextArrow: '<button type="button" aria-label="Next" class="arrow-down"><span class="mdi mdi-chevron-down-circle mdi-24px"></span><span>Scorri verso il basso</span></button>',
    })
var afterChange = function (slide, index) {
    $('.slick-slide').length;
}

$('#slider-scroll').on('afterChange', function (event, slick, currentSlide) {
    console.log(currentSlide);
    if (currentSlide !== 7) {
        $('.content-bottom-megamenu.collapse').collapse('hide');
        // $('.btn-megamenu').attr('aria-expanded', 'true');
    }
    // else {
    //     $('.content-bottom-megamenu.collapse').collapse('hide');
    //     // $('.btn-megamenu').attr('aria-expanded', 'false');
    // }

})


// $(function (event, slick, currentSlide) {
//     var currentSlide = 7;
//     if (currentSlide === 7) {
//         $('.content-bottom-megamenu.collapse').collapse('show');
//         // $('.btn-megamenu').attr('aria-expanded', 'true');
//     }
//     else {
//         $('.content-bottom-megamenu.collapse').collapse('hide');
//         // $('.btn-megamenu').attr('aria-expanded', 'false');
//     }
// });
// .on('afterChange', function (event, slick, currentSlide) {
//     if (slick.currentSlide >= slick.slideCount - slick.options.slidesToShow) {
//         $('.footer').removeClass('in');
//     }
//     else {
//         $('.footer').addClass('in');
//     }
// });

function mouseWheel($sliderScroll) {
    $(window).on('wheel', { slider: $sliderScroll }, mouseWheelHandler)
}