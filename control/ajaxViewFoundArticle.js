//#region Посылаем ajax запрос на выполение анализа
jQuery(document).ready(function ($) {

    $('#buttonGetFind').click(function(e) {
        e.preventDefault();

        var $mainBox = $('#contentContainerFoundArticle');

        $mainBox.animate({opacity: 0.5}, 300);
        
        $mainBox.addClass("loading");
        jQuery.post(
            myPlugin.ajaxurl,
            {
                action: 'getFoundArticle'
            },
            function (response) {
                $mainBox
                    .html(response)
                    .animate({opacity: 1}, 300)
                    .removeClass("loading");
            }
        ); 
    });
});
//#endregion
