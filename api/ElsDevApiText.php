<?php
//Функция получения абстракта статьи [23](DC:DESCRIPTION) в формате xml данных из Elaver developer
function apiElsDevText()
{
  set_time_limit(500);
  require_once plugin_dir_path(__FILE__) . '../bd/createText.php';
  require_once plugin_dir_path(__FILE__) . 'apiRequest.php';
  require_once plugin_dir_path(__FILE__) . '../bd/researchArticle.php';
  
  $query = array(
    "view" => "FULL",
  );
  $accept = " text/xml";
  $vals = researchArticle(); 

  foreach ($vals as $value){
    $scopusId = $value['scopus_id'];
    $url = "https://api.elsevier.com/content/article/scopus_id/$scopusId"; 
    $data = apiRequest($url, $apiKey, $query, $accept);
    createText($data, $value['id']);
  }
}