<?php
//Функция получения абстракта статьи
function apiElsDevAbstract()
{
  require_once plugin_dir_path(__FILE__) . '../bd/createAbstract.php';
  require_once plugin_dir_path(__FILE__) . '../bd/createText.php';
  require_once plugin_dir_path(__FILE__) . 'apiMultiRequest.php';  
  require_once plugin_dir_path(__FILE__) . 'apiRequest.php';
  require_once plugin_dir_path(__FILE__) . '../bd/researchArticle.php';
  
  $query = array(
    "view" => "FULL",
  );
  $queryUrl = http_build_query($query);
  $accept = " text/xml";    
  $vals = researchArticle();
  global $wpdb;
  
  foreach ($vals as $value){
    $scopusId = $value['scopus_id'];
    $url = "https://api.elsevier.com/content/article/scopus_id/$scopusId ";
    $data = apiRequest($url, $apiKey, $query, $accept);
    if ($data != false) {
      createAbstract($data);
      createText($data);
    } else {
      $wpdb->query( "DELETE FROM `wp_article` WHERE `scopus_id` = '$scopusId';" );
    }
  }

  $wpdb->query( "DELETE wp_journal FROM wp_journal LEFT JOIN wp_article ON wp_journal.id = wp_article.journal_id WHERE wp_article.id IS NULL" );
}

function apiElsDevAbstractAlt($journalId, $apiKey)
{
  require_once plugin_dir_path(__FILE__) . '../bd/createAbstract.php';
  require_once plugin_dir_path(__FILE__) . '../bd/createText.php';
  require_once plugin_dir_path(__FILE__) . 'apiMultiRequest.php';  
  require_once plugin_dir_path(__FILE__) . 'apiRequest.php';
  require_once plugin_dir_path(__FILE__) . '../bd/researchArticle.php';
  
  $query = array(
    "view" => "FULL",
  );
  $queryUrl = http_build_query($query);
  $accept = "text/xml";    
  global $wpdb;
  $user_id = get_current_user_id();
  $articles = $wpdb->get_results("SELECT wp_article.id, wp_article.scopus_id 
    FROM wp_article 
    WHERE wp_article.journal_id = '$journalId'", ARRAY_A);
    
  foreach ($articles as $value){
    $scopusId = $value['scopus_id'];
    $url = "https://api.elsevier.com/content/article/scopus_id/$scopusId ";
    $data = apiRequest($url, $apiKey, $query, $accept); 
    if ($data != "404" && $data != "400" && $data != "401" && $data != "402" && $data != "403" && $data != "405" && $data != "406" && $data) {
      createText($data);
      createAbstract($data);
    } else {
      $wpdb->query( "DELETE FROM `wp_article` WHERE `scopus_id` = '$scopusId' AND `user_id` = '$user_id';" );
    }
  }
}
