/**
 * Created by goodman on 10.01.14.
 */
$(function() {
    $('#slides').slidesjs({
        width: 700,
        height: 260,
        navigation: false,
        pagination: false,
        autoHeight: true,
        effect: {
            fade: {
                speed: 400
            }
        },
        play: {
            active: false,
            effect: "fade",
            interval: 5000,
            auto: true,
            swap: true,
            pauseOnHover: false,
            restartDelay: 2500
        }

    });
});

$('.desk-slider').textillate({

    loop: true,
    minDisplayTime: 2000,
    initialDelay: 0,
    autoStart: true,

    in: {
        effect: 'flipInX',

        delayScale: 1.5,

        delay: 150,

        sync: false,

        shuffle: false,

        reverse: false,

        callback: function () {}
    },

    // out animation settings.
    out: {
        effect: 'flipOutX',
        delayScale: 1.5,
        delay: 150,
        sync: false,
        shuffle: false,
        reverse: false,
        callback: function () {}
    },

    // callback that executes once textillate has finished
    callback: function () {}
});