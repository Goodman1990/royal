
$(document).ready(function() {
    $('#nav ul.tab').hover(
        function() {
            active= $('#nav ul.tab.active');
            $('#nav ul.tab').removeClass('active');
            $(this).addClass('active');
            $(this).find('.sub').addClass('active');

        }, function() {
            $('#nav ul.tab').removeClass('active');
            $(active).addClass('active');
        }
    );


    (function ($) {
        $.fn.jInputFile = function (options) {

            return this.each(function () {

                $(this).val('');
                $(this).wrap('<div></div>');
                $(this).parent().css('height', $(this).height());
                $(this).after('<div class="jInputFile-fakeButton"></div><div class="jInputFile-blocker"></div><div class="jInputFile-activeBrowseButton jInputFile-fakeButton"></div><div class="jInputFile-fileName"></div>');
                $(this).addClass('jInputFile-customFile');

                $(this).hover(
                    function () {
                        $(this).parent().children('.jInputFile-activeBrowseButton').css('display', 'block');
                    },
                    function () {
                        $(this).parent().children('.jInputFile-activeBrowseButton').css('display', 'none');
                    }
                );

                $(this).change(function () {
                    var file = $(this).val();

                    //Находим название файла и его расширение
                    var reWin = /.*\\(.*)/;
                    var fileName = file.replace(reWin, '$1');
                    var reUnix = /.*\/(.*)/;
                    fileName = fileName.replace(reUnix, '$1');
                    var regExExt = /.*\.(.*)/;
                    var ext = fileName.replace(regExExt, '$1');

                    //Показываем значок и имя файла
                    var pos;
                    if (ext) {
                        switch (ext.toLowerCase()) {
                            case 'doc':
                                pos = '0';
                                break;
                            case 'bmp':
                                pos = '16';
                                break;
                            case 'jpg':
                                pos = '32';
                                break;
                            case 'jpeg':
                                pos = '32';
                                break;
                            case 'png':
                                pos = '48';
                                break;
                            case 'gif':
                                pos = '64';
                                break;
                            case 'psd':
                                pos = '80';
                                break;
                            case 'mp3':
                                pos = '96';
                                break;
                            case 'wav':
                                pos = '96';
                                break;
                            case 'ogg':
                                pos = '96';
                                break;
                            case 'avi':
                                pos = '112';
                                break;
                            case 'wmv':
                                pos = '112';
                                break;
                            case 'flv':
                                pos = '112';
                                break;
                            case 'pdf':
                                pos = '128';
                                break;
                            case 'exe':
                                pos = '144';
                                break;
                            case 'txt':
                                pos = '160';
                                break;
                            default:
                                pos = '176';
                                break
                        }
                        ;
                        $(this).parent().children('.jInputFile-fileName').html(fileName).css({'background-position': ('0px -' + pos + 'px'), 'background-repeat': 'no-repeat', 'display': 'block'});
                    }
                    ;
                });
            });
        }
    })(jQuery);


});