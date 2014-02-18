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