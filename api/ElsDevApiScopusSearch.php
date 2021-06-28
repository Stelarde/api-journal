<?php
#region Функция получения списка статей в журналах в формате xml данных из Elaver developer
function apiElsDevScopusSearch()
{
  require_once plugin_dir_path(__FILE__) . 'apiRequest.php';
  require_once plugin_dir_path(__FILE__) . '../bd/createScopusSearch.php';
  require_once plugin_dir_path(__FILE__) . '../bd/researchSerial.php';
  
  global $wpdb;
  $url = 'https://api.elsevier.com/content/search/scopus'; 
  $accept = "application/xml";

  $journals = researchSerial();

  foreach ($journals as $journal){
    $titleJournal = $journal['name'];
    $query = array(
      "query" => "srctitle($titleJournal)",
    );
  
    $data = apiRequest($url, $apiKey, $query, $accept);
    if ($data != false) {
      createScopus($data, $journal['id']);
    } else {
      $journalId = $journal['id'];
      $wpdb->query( "DELETE FROM `wp_journal` WHERE `id` = '$journalId';" );
    }
  }
}
#endregion

function apiElsDevScopusSearchAlt($journals, $keywords, $opt, $fromYear, $toYear, $apiKey)
{
  require_once plugin_dir_path(__FILE__) . 'apiRequest.php';
  require_once plugin_dir_path(__FILE__) . '../bd/createScopusSearch.php';
  require_once plugin_dir_path(__FILE__) . '../bd/researchSerial.php';
  
  global $wpdb;
  $url = 'https://api.elsevier.com/content/search/scopus'; 
  $accept = "application/xml";
  $user_id = get_current_user_id();

  foreach ($journals as $value){
    $nameJournal = $value['name'];
    $journal = $wpdb->get_results("SELECT `name`, `journal_id` FROM `wp_select_journal_for_ex` WHERE `name` = '$nameJournal' AND `user_id` = '$user_id'", ARRAY_A);
    $titleJournal = $journal[0]['name'];
    if (!$keywords){
      if (($fromYear) && !($toYear)){
        $query = array(
          "query" => "srctitle($titleJournal) AND PUBYEAR > $fromYear",
          "count" => $opt
        );
      } 
      if (!($fromYear) && ($toYear)){
        $query = array(
          "query" => "srctitle($titleJournal) AND PUBYEAR < $toYear",
          "count" => $opt
        );
      }
      if (($fromYear) && ($toYear)){
        $query = array(
          "query" => "srctitle($titleJournal) AND PUBYEAR > $fromYear AND PUBYEAR < $toYear",
          "count" => $opt
        );
      }
      if (!($fromYear) && !($toYear)){
        $query = array(
          "query" => "srctitle($titleJournal)",
          "count" => $opt
        );
      }
    } else {
      if (!($fromYear) && !($toYear)){
        $query = array(
          "query" => "srctitle($titleJournal) AND TITLE-ABS-KEY-AUTH($keywords)",
          "count" => $opt
        );
      }
      if (($fromYear) && !($toYear)){
        $query = array(
          "query" => "srctitle($titleJournal) AND TITLE-ABS-KEY-AUTH($keywords) PUBYEAR > $fromYear",
          "count" => $opt
        );
      } 
      if (!($fromYear) && ($toYear)){
        $query = array(
          "query" => "srctitle($titleJournal) AND TITLE-ABS-KEY-AUTH($keywords) PUBYEAR < $toYear",
          "count" => $opt
        );
      }
      if (($fromYear) && ($toYear)){
        $query = array(
          "query" => "srctitle($titleJournal) AND TITLE-ABS-KEY-AUTH($keywords) PUBYEAR > $fromYear AND PUBYEAR < $toYear",
          "count" => $opt
        );
      }
    }
    
    $journalId = $journal[0]['journal_id'];
    $data = apiRequest($url, $apiKey, $query, $accept);
    if ($data != "404" && $data != "400" && $data != "401" && $data != "402" && $data != "403" && $data != "405" && $data != "406" && $data) {
      createScopus($data, $journal[0]['journal_id']);
      apiElsDevAbstractAlt($journal[0]['journal_id'], $apiKey);
    }
  }
}
