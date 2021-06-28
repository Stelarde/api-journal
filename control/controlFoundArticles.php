<?php
#region Функция получения названий журналов в формате xml данных из Elaver developer
add_action( 'wp_ajax_getFoundArticle', 'controlFoundArticles' );
add_action( 'wp_ajax_nopriv_getFoundArticle', 'controlFoundArticles' );
function controlFoundArticles(){
  set_time_limit(500000);
  require_once plugin_dir_path(__FILE__) . '../api/PyProjectFerquencyAnalysis.php';
  require_once plugin_dir_path(__FILE__) . '../view/viewFoundArticles.php';
  global $wpdb;
  $user_id = get_current_user_id();
  
  $result = json_decode(PyProjectFA($user_id), 1); 

  $wpdb->query( "DELETE FROM wp_result_analysis WHERE `user_id` = '$user_id'" );

  foreach($result as $key=>$value){
    $wpdb->insert('wp_result_analysis',
      array('article_id' => $key, 'result' => $value, 'user_id' => $user_id),
      array('%d', '%f', '%d')
    );
  }

  getResult();

  wp_die();
}

add_action( 'wp_enqueue_scripts', 'my_asset_for_controlFoundArticles');
function my_asset_for_controlFoundArticles() {
  wp_enqueue_script ( 
    'ajaxViewFoundArticle', 
    plugins_url( 'ajaxViewFoundArticle.js', __FILE__ ), 
    array( 'jquery' )
  );
  wp_localize_script( 'ajaxViewFoundArticle', 'myPlugin', 
    array(
      'ajaxurl' => admin_url( 'admin-ajax.php' )
    ) 
  );
} 
#endregion
