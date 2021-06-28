<?php
#region Поиск по загруженным статьям из Scopus
add_action( 'wp_ajax_searchJournalAlt', 'controlJournalSelectionSearchJournal' );
add_action( 'wp_ajax_nopriv_searchJournalAlt', 'controlJournalSelectionSearchJournal' );
function controlJournalSelectionSearchJournal() {
  set_time_limit(500000);
  require_once plugin_dir_path(__FILE__) . '../view/journalSelection.php'; 

  $search = ! empty( $_POST['link'] ) ? esc_attr( $_POST['link'] ) : false;
  global $wpdb;
  $searchResults = $wpdb->get_results("SELECT wp_select_journal_for_ex.id, wp_select_journal_for_ex.name
                                      FROM wp_select_journal_for_ex
                                      WHERE wp_select_journal_for_ex.name LIKE '%$search%'", ARRAY_A);
  
  itemSearchJournalAlt($searchResults);

  wp_die();
}

// Подключаем JS код
add_action( 'wp_enqueue_scripts', 'my_asset_for_search_journal_alt');
function my_asset_for_search_journal_alt() {
  wp_enqueue_script ( 
    'ajaxViewJournalSelect', 
    plugins_url( 'ajaxViewJournalSelect.js', __FILE__ ), 
    array( 'jquery' )
  );
  wp_localize_script( 'ajaxViewJournalSelect', 'myPlugin', 
    array(
      'ajaxurl' => admin_url( 'admin-ajax.php' )
    ) 
  );
} 
#endregion

#region Поиск похожих статей
add_action( 'wp_ajax_searchJournals', 'controlJournalSelection' );
add_action( 'wp_ajax_nopriv_searchJournals', 'controlJournalSelection' );
function controlJournalSelection() {
  set_time_limit(500000);
  global $wpdb;
  $selectJournal = array();
  $user_id = get_current_user_id();

  $selectJournallString = ! empty( $_POST['selectJournal'] ) ? esc_attr( $_POST['selectJournal'] ) : false;
  $selectJournal = explode('Text Separator1', $selectJournallString);

  $wpdb->query( "DELETE t1 FROM wp_select_journal t1 
    INNER JOIN wp_select_journal t2 
    WHERE t1.id < t2.id 
    AND t1.user_id = t2.user_id 
    AND t1.journal_id = t2.journal_id" );

  $selectJournals = $wpdb->get_results("SELECT `name`
    FROM `wp_select_journal` WHERE (`user_id` = '$user_id')", ARRAY_A);

    // $wpdb->query( "DELETE FROM `wp_select_journal` WHERE wp_select_journal.user_id = '$user_id'" );

    foreach($selectJournal as $key => $value){
      $journal = $value['name'];
      $journal = $wpdb->get_results("SELECT `name`, `id` FROM `wp_journal` WHERE `name` = '$journal'", ARRAY_A);
      $id = $journal[0]['id'];
      $wpdb->insert('wp_select_journal', array('name' => $journal, 'journal_id' => $id, 'user_id' => $user_id), array('%s', '%d', '%d'));
    }

    wp_die();
}

add_action( 'wp_enqueue_scripts', 'my_asset_for_search_journals');
function my_asset_for_search_journals() {
  wp_enqueue_script ( 
    'ajaxViewJournalSelect', 
    plugins_url( 'ajaxViewJournalSelect.js', __FILE__ ), 
    array( 'jquery' )
  );
  wp_localize_script( 'ajaxViewJournalSelect', 'myPlugin', 
    array(
      'ajaxurl' => admin_url( 'admin-ajax.php' )
    ) 
  );
} 
#endregion

#region Обработка нажатия checkbox
add_action( 'wp_ajax_checkboxClickAlt', 'controlScientificSearchCheckboxAlt' );
add_action( 'wp_ajax_nopriv_checkboxClickAlt', 'controlScientificSearchCheckboxAlt' );
function controlScientificSearchCheckboxAlt(){
  $select = ! empty( $_POST['selectJournal'] ) ? esc_attr( $_POST['selectJournal'] ) : false;
  $statusSelect = ! empty( $_POST['checkboxStatus'] ) ? esc_attr( $_POST['checkboxStatus'] ) : false;
  global $wpdb;
  $user_id = get_current_user_id();
  $journal = $wpdb->get_results("SELECT `id` FROM `wp_journal` WHERE `name` = '$select'", ARRAY_A);
  $id = $journal[0]['id'];
  if ($statusSelect){    
    $wpdb->insert('wp_select_journal', 
      array('name' => $select, 'journal_id' => $id, 'user_id' => $user_id ),
      array('%s', '%s', '%d', '%d')
    );
  } else{
    $wpdb->query("DELETE FROM wp_select_journal WHERE `user_id` = '$user_id' AND `journal_id` = $id");
  }  

  wp_die();
}
add_action( 'wp_enqueue_scripts', 'my_asset_for_checkboxClickAlt');
function my_asset_for_checkboxClickAlt() {
  wp_enqueue_script ( 
    'ajaxViewJournalSelect', 
    plugins_url( 'ajaxViewJournalSelect.js', __FILE__ ), 
    array( 'jquery' )
  );
  wp_localize_script( 'ajaxViewJournalSelect', 'myPlugin', 
    array(
      'ajaxurl' => admin_url( 'admin-ajax.php' )
    ) 
  );
} 
#endregion