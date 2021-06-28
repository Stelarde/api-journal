<?php
function researchSerial(){
    global $wpdb;
    $data = $wpdb->get_results("SELECT `id`, `name` FROM `wp_journal`", ARRAY_A);   

    return $data;
}
