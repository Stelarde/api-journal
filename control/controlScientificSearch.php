<?php
#region Поиск по загруженным статьям из Scopus
add_action( 'wp_ajax_searchJournal', 'controlScientificSearchJournal' );
add_action( 'wp_ajax_nopriv_searchJournal', 'controlScientificSearchJournal' );
function controlScientificSearchJournal() {
  require_once plugin_dir_path(__FILE__) . '../view/scientificSearch.php'; 
  $search = ! empty( $_POST['link'] ) ? esc_attr( $_POST['link'] ) : false;
  global $wpdb;
  $user_id = get_current_user_id();

  $searchResultsJ = $wpdb->get_results("SELECT wp_journal.id
    FROM wp_article
    INNER JOIN wp_select_journal_for_ex
    ON wp_article.journal_id = wp_select_journal_for_ex.journal_id
    INNER JOIN wp_journal
    ON wp_select_journal_for_ex.journal_id = wp_journal.id
    AND wp_select_journal_for_ex.user_id = '$user_id'
    WHERE wp_journal.name LIKE '%$search%' 
    OR wp_article.name LIKE '%$search%'
    OR wp_article.abstract LIKE '%$search%'", ARRAY_A);

  $searchResultsA = $wpdb->get_results("SELECT wp_article.id, wp_article.abstract
    FROM wp_article
    INNER JOIN wp_select_journal_for_ex
    ON wp_article.journal_id = wp_select_journal_for_ex.journal_id
    INNER JOIN wp_journal
    ON wp_select_journal_for_ex.journal_id = wp_journal.id
    AND wp_select_journal_for_ex.user_id = '$user_id'
    WHERE wp_journal.name LIKE '%$search%' 
    OR wp_article.name LIKE '%$search%'
    OR wp_article.abstract LIKE '%$search%'", ARRAY_A);

  $searchResultJournal = array();
  $searchResultArticle = array();

  foreach($searchResultsJ as $key=>$searchResult){
    $id = $searchResult['id'];
    $name = $wpdb->get_results("SELECT wp_journal.name FROM wp_journal WHERE wp_journal.id = '$id'", ARRAY_A);
    $searchResultJournal[$key] = [
      "id" => $id,
      "name" => $name[0]['name']
    ];
  }

  foreach($searchResultsA as $key=>$searchResult){
    $idArticle = $searchResult['id'];
    $article = $wpdb->get_results("SELECT wp_article.name, wp_article.journal_id
      FROM wp_article WHERE wp_article.id = '$idArticle'", ARRAY_A);
    $searchResultArticle[$key] = [
      "id" => $searchResult['id'],
      "name" => $article[0]['name'],
      "abstract" => $searchResult['abstract'],
      "Journal_id" => $article[0]['journal_id']
    ];
  }
  
  $searchResultJournal = array_unique($searchResultJournal, SORT_REGULAR);
  
  itemSearchJournal($searchResultJournal, $searchResultArticle, $search);

  wp_die();
}

// Подключаем JS код
add_action( 'wp_enqueue_scripts', 'my_asset_for_search_journal');
function my_asset_for_search_journal() {
  wp_enqueue_script ( 
    'ajaxViewSearch', 
    plugins_url( 'ajaxViewSearch.js', __FILE__ ), 
    array( 'jquery' )
  );
  wp_localize_script( 'ajaxViewSearch', 'myPlugin', 
    array(
      'ajaxurl' => admin_url( 'admin-ajax.php' )
    ) 
  );
} 
#endregion

#region Поиск по опубликованным статьям на сайте
add_action( 'wp_ajax_searchPublishedArticles', 'controlScientificSearchPublishedArticle' );
add_action( 'wp_ajax_nopriv_searchPublishedArticles', 'controlScientificSearchPublishedArticle' );
function controlScientificSearchPublishedArticle(){
  require_once plugin_dir_path(__FILE__) . '../view/scientificSearch.php';
  global $wpdb;
  $search = ! empty( $_POST['link'] ) ? esc_attr( $_POST['link'] ) : false;
  $publishArticles = $wpdb->get_results("SELECT `post_title`, `guid`
                                          FROM `wp_posts`
                                          WHERE `post_type` = 'post'
                                          AND `post_status` = 'publish'
                                          AND `post_title` LIKE '%$search%'", ARRAY_A);

  
  itemSearchArticle($publishArticles, $search);
  wp_die();
}

add_action( 'wp_enqueue_scripts', 'my_asset_for_search_published_article');
function my_asset_for_search_published_article() {
  wp_enqueue_script ( 
    'ajaxViewSearch', 
    plugins_url( 'ajaxViewSearch.js', __FILE__ ), 
    array( 'jquery' )
  );
  wp_localize_script( 'ajaxViewSearch', 'myPlugin', 
    array(
      'ajaxurl' => admin_url( 'admin-ajax.php' )
    ) 
  );
} 
#endregion

#region Поиск похожих статей
add_action( 'wp_ajax_search', 'controlScientificSearch' );
add_action( 'wp_ajax_nopriv_search', 'controlScientificSearch' );
function controlScientificSearch() {
  set_time_limit(500000);
  require_once plugin_dir_path(__FILE__) . '../api/PyProjectFerquencyAnalysis.php';
  require_once plugin_dir_path(__FILE__) . '../view/journalSelection.php';
  global $wpdb;
  $user_id = get_current_user_id();
  $selectJournal = array();
  $selectArticle = array();
  $selectPublishedArticle = array();
  $dataJournal = array();
  $dataArticle = array();
  $dataPublishedArticle = array();
  $stringDataJournal = "";
  $stringDataArticle = "";
  $stringDataPublishedArticle = "";

  $search = ! empty( $_POST['line'] ) ? esc_attr( $_POST['line'] ) : false;

  $dataJournal= $wpdb->get_results("SELECT wp_article_section_text.text 
                      FROM wp_article_section_text 
                      INNER JOIN wp_article_section_title 
                      ON wp_article_section_text.article_title_id = wp_article_section_title.id 
                      INNER JOIN wp_article 
                      ON wp_article_section_title.wp_article_id = wp_article.id 
                      INNER JOIN wp_select_journal_for_sample
                      ON wp_article.journal_id = wp_select_journal_for_sample.journal_id
                      WHERE wp_select_journal_for_sample.user_id = '$user_id'", ARRAY_A); 
  if ($dataJournal){
    foreach ($dataJournal as $valueData){
      $stringDataJournal  = empty( $stringDataJournal) ? implode(" ", $valueData) : $stringDataJournal . ' ' . implode(" ", $valueData);
    }
  }

  $dataArticle = $wpdb->get_results("SELECT wp_article_section_text.text 
                                          FROM wp_article_section_text 
                                          INNER JOIN wp_article_section_title 
                                          ON wp_article_section_text.article_title_id = wp_article_section_title.id 
                                          INNER JOIN wp_article_select 
                                          ON wp_article_section_title.wp_article_id = wp_article_select.article_id
                                          WHERE wp_article_select.user_id = '$user_id'", ARRAY_A);
  if ($dataArticle){
    foreach ($dataArticle as $valueData){
      $stringDataArticle  = empty($stringDataArticle) ? implode(" ", $valueData) : $stringDataArticle . ' ' . implode(" ", $valueData);       
    }
  }                                    

  $dataPublishedArticle = $wpdb->get_results("SELECT `post_content`
                                              FROM `wp_posts` 
                                              INNER JOIN `wp_select_post`
                                              ON wp_select_post.post_id = wp_posts.id
                                              WHERE wp_posts.post_type = 'post' 
                                              AND wp_posts.post_status = 'publish' 
                                              AND wp_select_post.user_id = '$user_id'", ARRAY_A);
  if ($dataPublishedArticle){
    foreach ($dataPublishedArticle as $valueData){
      $stringDataJournal  = empty( $stringDataJournal) ? implode(" ", $valueData) : $stringDataJournal . ' ' . implode(" ", $valueData);
    } 
  }                                              

  $sample = $search . ' ' . $stringDataJournal . ' ' . $stringDataArticle . ' ' . $stringDataPublishedArticle;
  $sample = trim($sample);
  $wpdb->query( "DELETE FROM `wp_sample` WHERE `user_id` = '$user_id'" );
  $wpdb->insert('wp_sample', 
    array('string' => $sample, 'user_id' => $user_id)
  );

  wp_die();
}

add_action( 'wp_enqueue_scripts', 'my_asset_for_search');
function my_asset_for_search() {
  wp_enqueue_script ( 
    'ajaxViewSearch', 
    plugins_url( 'ajaxViewSearch.js', __FILE__ ), 
    array( 'jquery' )
  );
  wp_localize_script( 'ajaxViewSearch', 'myPlugin', 
    array(
      'ajaxurl' => admin_url( 'admin-ajax.php' )
    ) 
  );
} 
#endregion

#region Форма списка статей в журнале, формы ввода своего текста, форма выбора из опубликованного контента
add_action( 'wp_ajax_getArticles', 'controlScientificSearchGetArticles' );
add_action( 'wp_ajax_nopriv_getArticles', 'controlScientificSearchGetArticles' );
function controlScientificSearchGetArticles() {
  set_time_limit(500000);
  require_once plugin_dir_path(__FILE__) . '../view/scientificSearch.php';
  require_once plugin_dir_path(__FILE__) . '../api/ElsDevApiScopusSearch.php';
  global $wpdb;
  $user_id = get_current_user_id();
  $keywords = ! empty( $_POST['keywords'] ) ? esc_attr( $_POST['keywords'] ) : false;
  $quantity = ! empty( $_POST['quantity'] ) ? esc_attr( $_POST['quantity'] ) : false;
  $fromYear = ! empty( $_POST['fromYear'] ) ? esc_attr( $_POST['fromYear'] ) : false;
  $toYear = ! empty( $_POST['toYear'] ) ? esc_attr( $_POST['toYear'] ) : false;
  $api = ! empty( $_POST['api'] ) ? esc_attr( $_POST['api'] ) : false;
  $wpdb->query( "DELETE t1 FROM wp_select_journal_for_ex t1 
    INNER JOIN wp_select_journal_for_ex t2 
    WHERE t1.id < t2.id 
    AND t1.user_id = t2.user_id 
    AND t1.journal_id = t2.journal_id " );
  $selectJournals = $wpdb->get_results("SELECT `name`
    FROM `wp_select_journal_for_ex` WHERE (`user_id` = '$user_id')", ARRAY_A);

  $arrayColArticle = [
    "1" => "10",
    "2" => "15",
    "3" => "25"
  ];

  if ($quantity){
    $quantity = $arrayColArticle["$quantity"];
  } else {
    $quantity = "10";
  }

  if (!$api){
    $api = "e8d8b8c1bc7969b9827b72bc22a9541a";
  }

  foreach($selectJournals as $selectJournal){
    $value = $selectJournal['name'];
    $control = $wpdb->get_results("SELECT `name`, `number_of_articles`, `from_year`, `to_year`, `keywords`, `user_id`
      FROM wp_change_control
      WHERE `user_id` = '$user_id'
      AND `name` = '$value'
      AND `number_of_articles` = '$quantity'
      AND `from_year` = '$fromYear'
      AND `to_year` = '$toYear'
      AND `keywords` = '$keywords'", ARRAY_A);
    if (empty($control)){
      $wpdb->query( "DELETE FROM wp_settings_load_article WHERE `user_id` = '$user_id'" );
      $wpdb->query( "DELETE FROM wp_change_control WHERE `user_id` = '$user_id'" );
      $wpdb->query( "DELETE FROM wp_article WHERE `user_id` = '$user_id'" );

      $wpdb->insert('wp_settings_load_article', 
        array('number_of_articles' => $quantity, 'from_year' => $fromYear, 'to_year' => $toYear, 'keywords' => $keywords, 'user_id' => $user_id),
        array('%d', '%d', '%d', '%s', '%d')
      );

      foreach($selectJournals as $selectJournal){
        $value = $selectJournal['name'];
        $wpdb->insert('wp_change_control',
          array('name' => $value, 'number_of_articles' => $quantity, 'from_year' => $fromYear, 'to_year' => $toYear, 'keywords' => $keywords, 'user_id' => $user_id),
          array('%s', '%d', '%d', '%d', '%s', '%d')  
        );
      }

      $fromYear -= 1;
      $toYear += 1;

      apiElsDevScopusSearchAlt($selectJournals, $keywords, $quantity, $fromYear, $toYear, $api);
      
      break;
    }
  }

  getScientificSearchPageGetArticles();

  wp_die();
}

add_action( 'wp_enqueue_scripts', 'my_asset_for_getArticles');
function my_asset_for_getArticles() {
  wp_enqueue_script ( 
    'ajaxViewSearch', 
    plugins_url( 'ajaxViewSearch.js', __FILE__ ), 
    array( 'jquery' )
  );
  wp_localize_script( 'ajaxViewSearch', 'myPlugin', 
    array(
      'ajaxurl' => admin_url( 'admin-ajax.php' )
    ) 
  );
} 
#endregion

#region Обработка нажатия checkbox
add_action( 'wp_ajax_checkboxClick', 'controlScientificSearchCheckbox' );
add_action( 'wp_ajax_nopriv_checkboxClick', 'controlScientificSearchCheckbox' );
function controlScientificSearchCheckbox(){
  $select = ! empty( $_POST['selectJournal'] ) ? esc_attr( $_POST['selectJournal'] ) : false;
  $statusSelect = ! empty( $_POST['checkboxStatus'] ) ? esc_attr( $_POST['checkboxStatus'] ) : false;
  $classSelect = ! empty( $_POST['checkboxClass'] ) ? esc_attr( $_POST['checkboxClass'] ) : false;
  global $wpdb;
  $user_id = get_current_user_id();
  switch($classSelect) {
    case 'checkJournalForEx':
      $journal = $wpdb->get_results("SELECT `id` FROM `wp_journal` WHERE `name` = '$select'", ARRAY_A);
      $id = $journal[0]['id'];
      $availability = $wpdb->get_results("SELECT `id` FROM `wp_select_journal_for_ex` WHERE `journal_id` = '$id' AND `user_id` = '$user_id'", ARRAY_A);
      if ($statusSelect == "true" ){    
        $wpdb->insert('wp_select_journal_for_ex', 
          array('name' => $select, 'journal_id' => $id, 'user_id' => $user_id ),
          array('%s', '%d', '%d')
        );
      } else{
        $wpdb->query("DELETE FROM wp_select_journal_for_ex WHERE `user_id` = '$user_id' AND `journal_id` = $id");
      }
      break;
    case 'checkJournal':
      $journal = $wpdb->get_results("SELECT `id` FROM `wp_select_journal_for_ex` WHERE `name` = '$select'", ARRAY_A);
      $id = $journal[0]['id'];
      $availability = $wpdb->get_results("SELECT `id` FROM `wp_select_journal_for_sample` WHERE `journal_id` = '$id' AND `user_id` = '$user_id'", ARRAY_A);
      if ($statusSelect == "true" && empty($availability[0]['id'])){    
        $wpdb->insert('wp_select_journal_for_sample', 
          array('journal_id' => $id, 'user_id' => $user_id),
          array('%d', '%d')
        );
      } else{
        $wpdb->query("DELETE FROM wp_select_journal_for_sample WHERE `user_id` = '$user_id' AND `journal_id` = $id");
      }
      break;
    case 'checkArticle':
      $article = $wpdb->get_results("SELECT `id` FROM `wp_article` WHERE `name` = '$select' AND `user_id` = '$user_id'", ARRAY_A);
      $id = $article[0]['id'];
      $availability = $wpdb->get_results("SELECT `id` FROM `wp_article_select` WHERE `article_id` = '$id' AND `user_id` = '$user_id'", ARRAY_A);
      if ($statusSelect == "true"){
        $wpdb->insert('wp_article_select',
          array('article_id' => $id, 'user_id' => $user_id ),
          array('%d', '%d')
        );
        $wpdb->query( "DELETE t1 FROM wp_article_select t1 
          INNER JOIN wp_article_select t2 
          WHERE t1.id < t2.id 
          AND t1.user_id = t2.user_id 
          AND t1.article_id = t2.article_id " );
      } else{
        $wpdb->query("DELETE FROM wp_article_select WHERE `user_id` = '$user_id' AND `article_id` = $id");
      }
      break;
    case 'checkPublishedArticle':
      $article = $wpdb->get_results("SELECT `id` FROM `wp_posts` WHERE `post_title` = '$select'", ARRAY_A);
      $id = $article[0]['id'];
      $availability = $wpdb->get_results("SELECT `id` FROM `wp_select_post` WHERE `post_id` = '$id' AND `user_id` = '$user_id'", ARRAY_A);
      if ($statusSelect == "true" && empty($availability[0]['id'])){
        $wpdb->insert('wp_select_post',
          array('post_id' => $id, 'user_id' => $user_id),
          array('%d', '%d')
        );
      } else{
        $wpdb->query("DELETE FROM wp_select_post WHERE `user_id` = '$user_id' AND `post_id` = $id");
      }
      break;
  }

  wp_die();
}

add_action( 'wp_enqueue_scripts', 'my_asset_for_checkboxClick');
function my_asset_for_checkboxClick() {
  wp_enqueue_script ( 
    'ajaxViewSearch', 
    plugins_url( 'ajaxViewSearch.js', __FILE__ ), 
    array( 'jquery' )
  );
  wp_localize_script( 'ajaxViewSearch', 'myPlugin', 
    array(
      'ajaxurl' => admin_url( 'admin-ajax.php' )
    ) 
  );
} 
#endregion

#region Поиск во всех журналах для предварительной загрузки
add_action( 'wp_ajax_searchJournalForEx', 'controlScientificSearchForEx' );
add_action( 'wp_ajax_nopriv_searchJournalForEx', 'controlScientificSearchForEx' );
function controlScientificSearchForEx(){
  require_once plugin_dir_path(__FILE__) . '../view/scientificSearch.php';
  $search = ! empty( $_POST['link'] ) ? esc_attr( $_POST['link'] ) : false;
  global $wpdb;
  $searchResult = $wpdb->get_results("SELECT wp_journal.id, wp_journal.name
    FROM wp_journal
    WHERE wp_journal.name LIKE '%$search%'", ARRAY_A);
  getScientificSearchPageForEx($searchResult);
  wp_die();
}

add_action( 'wp_enqueue_scripts', 'my_asset_for_searchFOrEx');
function my_asset_for_searchFOrEx() {
  wp_enqueue_script ( 
    'ajaxViewSearch', 
    plugins_url( 'ajaxViewSearch.js', __FILE__ ), 
    array( 'jquery' )
  );
  wp_localize_script( 'ajaxViewSearch', 'myPlugin', 
    array(
      'ajaxurl' => admin_url( 'admin-ajax.php' )
    ) 
  );
} 
#endregion
