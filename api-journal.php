<?php

/*
  Plugin Name: Magazines from scopus
*/

#region Подключаем необходимые скрипты и стили 
add_action( 'wp_enqueue_scripts', 'my_scripts_method' ); //Подключение бутстрапа
function my_scripts_method(){
  wp_enqueue_style( 
    'bs', 
    'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css', 
    false, 
    '5.0.0', 
    'all' 
  );
  wp_enqueue_script( 
    'popper', 
    'https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js', 
    array('jquery'), 
    '2.5.4', 
    true 
  );
  wp_enqueue_script( 
    'bs', 
    'https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.min.js', 
    array('popper'), 
    '5.0.0', 
    true 
  );
}
#endregion

//Отображение статей журнала 
require_once plugin_dir_path(__FILE__) . 'view/viewingArticles.php'; 
//Отображение журналов
require_once plugin_dir_path(__FILE__) . 'view/viewingJournals.php'; 
//Отображение поиска
require_once plugin_dir_path(__FILE__) . 'view/scientificSearch.php'; 
//Выбор журналов
require_once plugin_dir_path(__FILE__) . 'view/journalSelection.php';
//Результат поиска
require_once plugin_dir_path(__FILE__) . 'view/viewFoundArticles.php';
//Запрос api, список журналов
require_once plugin_dir_path(__FILE__) . 'api/ElsDevApiSerial.php'; 
//Запрос api, список статей журналов
require_once plugin_dir_path(__FILE__) . 'api/ElsDevApiScopusSearch.php'; 
//Запрос api, список абстрактов статей
require_once plugin_dir_path(__FILE__) . 'api/ElsDevApiAbstract.php'; 
//Запрос api, тексты статей
require_once plugin_dir_path(__FILE__) . 'api/ElsDevApiText.php';
//Контроль окна ввода данные для поиска схожих статей
require_once plugin_dir_path(__FILE__) . 'control/controlScientificSearch.php'; 
//Контроль окна выбора журналов
require_once plugin_dir_path(__FILE__) . 'control/controlJournalSelection.php'; 
//Контроль окна результата поиска
require_once plugin_dir_path(__FILE__) . 'control/controlFoundArticles.php';
?>