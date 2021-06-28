<?php
function createScopus($vals, $id)
{
    global $wpdb;    
    $user_id = get_current_user_id();   
    foreach($vals as $value) {
        if ($value["tag"] == "DC:IDENTIFIER"){
            $scopusId = $value["value"];
        }
        if ($value["tag"] == "DC:TITLE"){
            $name = trim($value["value"]);  
            $checkScopusId = substr($scopusId, 10);
            $check = $wpdb->get_results("SELECT wp_article.id, wp_article.scopus_id 
                FROM wp_article 
                WHERE wp_article.scopus_id = '$checkScopusId' AND wp_article.user_id = '$user_id'", ARRAY_A);
            if ($check[0]['scopus_id']){
                $scopusId = "";
                continue 1;
            } else {
                $wpdb->insert('wp_article', 
                    array('name' => $name, 'scopus_id' => substr($scopusId, 10), 'journal_id' => $id, 'user_id' => $user_id),
                    array('%s', '%s', '%d', '%d')
                );
            }
        }
    }
}