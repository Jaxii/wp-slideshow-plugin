jQuery(document).ready(function($) {
    $('.custom-slideshow').each(function() {
        var $slideshow = $(this);
        var type = $slideshow.data('type');
        var options = {
            dots: true,
            arrows: true,
            infinite: true,
            speed: 500,
            slidesToShow: 1,
            slidesToScroll: 1,
            prevArrow: '<button type="button" class="slick-prev">&larr;</button>',
            nextArrow: '<button type="button" class="slick-next">&rarr;</button>',
            adaptiveHeight: false
        };

        if (type === 'carousel') {
            options.slidesToShow = 3;
            options.slidesToScroll = 3;
            options.responsive = [
                {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        slidesToShow: 1,
                        slidesToScroll: 1
                    }
                }
            ];
        }

        $slideshow.on('init', function(event, slick) {
            resizeSlideshow($slideshow);
        });

        $slideshow.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
            resizeSlideshow($slideshow, nextSlide);
        });

        $slideshow.slick(options);
    });

    function resizeSlideshow($slideshow, nextSlide) {
        var $container = $slideshow.closest('.custom-slideshow-container');
        var $currentSlide = nextSlide !== undefined ? 
            $slideshow.find('.slick-slide[data-slick-index="' + nextSlide + '"]') : 
            $slideshow.find('.slick-current');
        var $img = $currentSlide.find('img');
        
        var originalWidth = parseInt($img.data('original-width'));
        var originalHeight = parseInt($img.data('original-height'));
        var isHorizontal = $img.data('is-horizontal') === 'true';
        var windowWidth = $(window).width();
        var windowHeight = $(window).height();

        var scaleFactor = 1;
        if (isHorizontal) {
            scaleFactor = Math.min(windowWidth / originalWidth, windowHeight / originalHeight, 2);
        } else {
            scaleFactor = Math.min(windowWidth / originalWidth, windowHeight / originalHeight, 1);
        }

        var newWidth = originalWidth * scaleFactor;
        var newHeight = originalHeight * scaleFactor;

        $container.css({
            'width': newWidth + 'px',
            'height': newHeight + 'px'
        });

        $slideshow.find('.slick-list, .slick-track').height(newHeight);

        $img.css({
            'width': newWidth + 'px',
            'height': newHeight + 'px'
        });
    }

    $(window).on('resize', function() {
        $('.custom-slideshow').each(function() {
            resizeSlideshow($(this));
        });
    });
});