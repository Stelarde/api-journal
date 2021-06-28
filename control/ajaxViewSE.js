//#region Посылаем ajax запрос
jQuery(document).ready(function ($) {

    var $mainBox = $('#contentContainerSearchJournal');

    var $input = $('#searchJournalorArticle');

    $('#getBatonSearchJournal').click(function(e) {
        e.preventDefault();

        var search = $input.val();

        $mainBox.animate({opacity: 0.5}, 300);

        jQuery.post(
            myPlugin.ajaxurl,
            {
                action: 'searchJournal',
                link: search
            },
            function (response) {
                $mainBox
                    .html(response)
                    .animate({opacity: 1}, 300);
            }
        );
    }); 
});
//#endregion
