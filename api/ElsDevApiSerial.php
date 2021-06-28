<?php
//Отображение журналов

#region Функция получения названий журналов в формате xml данных из Elaver developer
add_action( 'wp_ajax_getJournal', 'apiElsDevSerial' );
add_action( 'wp_ajax_nopriv_getJournal', 'apiElsDevSerial' );
function apiElsDevSerial()
{
  set_time_limit(300000);
  require_once plugin_dir_path(__FILE__) . '../bd/createSerial.php';
  require_once plugin_dir_path(__FILE__) . 'apiMultiRequest.php';
  require_once plugin_dir_path(__FILE__) . '../view/viewingJournals.php'; 
  require_once plugin_dir_path(__FILE__) . 'ElsDevApiScopusSearch.php';
  require_once plugin_dir_path(__FILE__) . 'ElsDevApiAbstract.php';

  $url = 'https://api.elsevier.com/content/serial/title/'; 
  $apiKey = 'e8d8b8c1bc7969b9827b72bc22a9541a';
  $accept = 'text/xml';
  $queries = array();
  $inputParams = array();
  $start = 0;
  for ($i = 1; $i <= 2; $i++){
    $queries[$i] = [
      "subj" => "ENER",
      "count" => "200",
      "start" => $start
    ];
    $start = $start + 200;

    $inputParams[$i] = [
      "url" => $url,
      "apiKey" => $apiKey,
      "accept" => $accept,
      "queryUrl" => http_build_query($queries[$i])
    ]; 
  }
  $data = apiMultiRequest($inputParams);
  getJournal();
  createSerial($data);  
  wp_die();
}
#endregion

#region Подключаем JS код
add_action( 'wp_enqueue_scripts', 'my_asset');
function my_asset() {
  wp_enqueue_script ( 
    'ajaxViewJR', 
    plugins_url( '../control/ajaxViewJR.js', __FILE__ ), 
    array( 'jquery' )
  );
  wp_localize_script( 'ajaxViewJR', 'myPlugin', 
    array(
      'ajaxurl' => admin_url( 'admin-ajax.php' )
    ) 
  );
} 
#endregion
