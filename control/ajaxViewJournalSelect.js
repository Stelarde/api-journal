//#region Посылаем ajax запрос на поиск в загруженных журналах
jQuery(document).ready(function ($) {

    var $mainBox = $('#contentContainerJournals');

    var $input = $('#searchJournalAlt');

    $('#getBatonSearchJournalAlt').click(function(e) {
        e.preventDefault();

        var search = $input.val();

        $mainBox.animate({opacity: 0.5}, 300);

        jQuery.post(
            myPlugin.ajaxurl,
            {
                action: 'searchJournalAlt',
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

//#region Посылаем ajax запрос с выбранным журналами
jQuery(document).ready(function ($) {

    var $mainBox = $('#contentContainerSelectJournal');

    $('#batonFind').click(function(e) {
        // e.preventDefault();

        var checkboxValueJournal = new Array();

        $mainBox.animate({opacity: 0.5}, 300);

        $('input:checkbox:checked').each(function(){
            checkboxValueJournal.push($(this).val());
        });

        checkboxValueJournal = checkboxValueJournal.join('Text Separator1');

        jQuery.post(
            myPlugin.ajaxurl,
            {
                action: 'searchJournals',
                selectJournal: checkboxValueJournal
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

//#region Посылаем ajax запрос на выделение всех чекбоксов статей в журнале
jQuery(document).ready(function ($) {
    // $('input:checkbox:checked').on('change', function(e) {
    $('div').on('change', 'input:checkbox', function(e) {
        e.preventDefault();
        checkboxValueJournal = $(this).attr('value');
        checkboxStatus = $(this).prop('checked');
        jQuery.post(
            myPlugin.ajaxurl,
            {
                action: 'checkboxClickAlt',
                selectJournal: checkboxValueJournal,
                checkboxStatus: checkboxStatus
            }
        );
    });
}); 
//#endregion
