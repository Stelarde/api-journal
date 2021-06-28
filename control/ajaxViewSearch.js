//#region Посылаем ajax запрос на поиск опубликованных статей
jQuery(document).ready(function ($) {
    $('div').on('click', '#getBatonSearchPublishedArticles', function(e) {
        e.preventDefault();

        var $mainBox = $('#contentContainerSearchPublishedArticles');

        var $input = $('#searchPublishedArticles');

        var search = $input.val();

        $mainBox.animate({opacity: 0.5}, 300);

        jQuery.post(
            myPlugin.ajaxurl,
            {
                action: 'searchPublishedArticles',
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

//#region Посылаем ajax запрос на поиск во всех журналах для предварительной загрузки
jQuery(document).ready(function ($) {

    var $mainBox = $('#contentContainerJournalsForEx');

    $('#getBatonSearchJournalForEx').click(function(e) {
        e.preventDefault();

        var $input = $('#searchJournalForEx');

        var search = $input.val();

        $mainBox.animate({opacity: 0.5}, 300);

        jQuery.post(
            myPlugin.ajaxurl,
            {
                action: 'searchJournalForEx',
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

//#region Посылаем ajax запрос на поиск в загруженных журналах
jQuery(document).ready(function ($) {

    $('div').on('click', '#getBatonSearchJournal', function(e) {
        e.preventDefault();
        var $mainBox = $('#contentContainerSearchJournal');

        var $input = $('#searchJournalorArticle');

        var search = $input.val();

        $mainBox.animate({opacity: 0.5}, 300);

        jQuery.post(
            myPlugin.ajaxurl,
            {
                action: 'searchJournal',
                link: search,
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

//#region Посылаем ajax запрос на поиск похожих статей
jQuery(document).ready(function ($) {

    $('div').on('click', '#batonNext', function(e) {
        var $mainBox = $('#contentContainerSearch');

        var $textarea = $('#exampleFormControlTextarea1');

        var checkboxValueJournal = new Array();
        var checkboxValueArticle = new Array();
        var checkboxValuePublishedArticle = new Array();
        var searchLine = $textarea.val();

        $mainBox.animate({opacity: 0.5}, 300);

        $('input:checkbox:checked').each(function(){
            switch($(this).attr('class')){
                case 'checkJournal':
                    checkboxValueJournal.push($(this).val());
                    break;
                case 'checkArticle':
                    checkboxValueArticle.push($(this).val());
                    break;
                case 'checkPublishedArticle':
                    checkboxValuePublishedArticle.push($(this).val());
                    break;
            }
        });

        checkboxValueJournal = checkboxValueJournal.join('Text Separator1');
        checkboxValueArticle = checkboxValueArticle.join('Text Separator1');
        checkboxValuePublishedArticle = checkboxValuePublishedArticle.join('Text Separator1');

        jQuery.post(
            myPlugin.ajaxurl,
            {
                action: 'search',
                line: searchLine,
                selectJournal: checkboxValueJournal,
                selectArticle: checkboxValueArticle,
                selectPublishedArticle: checkboxValuePublishedArticle
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

//#region Регистрируем нажатие checkbox
jQuery(document).ready(function ($) {
    $('div').on('change', 'input:checkbox', function(e) {
        checkboxValueJournal = $(this).attr('value');
        checkboxStatus = $(this).prop('checked');
        checkboxClass = $(this).attr('class');
        jQuery.post(
            myPlugin.ajaxurl,
            {
                action: 'checkboxClick',
                selectJournal: checkboxValueJournal,
                checkboxStatus: checkboxStatus,
                checkboxClass: checkboxClass
            }
        );
    });
}); 
//#endregion

//#region Посылаем ajax запрос на получение формы списка статей в журнале, формы ввода своего текста, форма выбора из опубликованного контента
jQuery(document).ready(function ($) {
    var $mainBox = $('#contentContainerSearch');

    $('#batonGetArticle').click(function(e) {
        e.preventDefault();

        $mainBox.animate({opacity: 0.5}, 300);

        var $select = $('#inputGroupSelect');

        var $fromYear = $('#inpFromYear').val();

        var $toYear = $('#inpToYear').val();

        var $keywords = $('#inputKeywords').val();

        var $api = $('#inputAPI').val();

        var $quantity = $select.find('option:selected').val();

        jQuery.post(
            myPlugin.ajaxurl,
            {
                action: 'getArticles',
                keywords: $keywords,
                quantity: $quantity,
                fromYear: $fromYear,
                toYear: $toYear,
                api: $api
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
