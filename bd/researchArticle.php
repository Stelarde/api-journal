<?php
function researchArticle(){
    global $wpdb;
    $data = $wpdb->get_results("SELECT `id`, `scopus_id` FROM `wp_article`", ARRAY_A);   

    return $data;
}
