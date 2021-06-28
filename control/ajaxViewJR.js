//#region Посылаем ajax запрос
jQuery(document).ready(function ($) {

    var $mainBox = $('#contentContainer');

    $('#get_baton').click(function(e) {
        e.preventDefault();

        var linkJournal = $(this).attr('href');

        $mainBox.animate({opacity: 0.5}, 300);
        
        $mainBox.addClass("loading");

        $(this).text('Load...');

        jQuery.post(
            myPlugin.ajaxurl,
            {
                action: 'getJournal',
                link: linkJournal
            },
            function (response) {
                $(this).text('Refresh');
                $mainBox
                    .html(response)
                    .animate({opacity: 1}, 300);
                $mainBox.removeClass("loading");
            }
        );
    }); 
});
//#endregion
