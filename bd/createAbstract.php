<?php
function createAbstract($vals)
{
    global $wpdb;
    $k = 0;
    if ($vals){
        foreach($vals as $value) {   
            switch ($value["tag"]) {
                case "SERVICE-ERROR":
                    break 1;
                case "DC:DESCRIPTION":
                    if (isset($value['value'])){
                        $abstract = trim($value['value']); 
                    }                    
                    continue 2;                  
                case "SCOPUS-ID":
                    $scopusId = $value["value"]; 
                    $check = $wpdb->get_results("SELECT wp_article.id, wp_article.abstract 
                        FROM wp_article 
                        WHERE wp_article.scopus_id = '$scopusId' AND wp_article.user_id = '$user_id'", ARRAY_A);
                    if (isset($check[0])){
                        break 2;
                    } else {
                        if (isset($abstract)){
                            $wpdb->update('wp_article',
                            array('abstract' => $abstract),
                            array('scopus_id' => $scopusId)
                            );  
                        } 
                    }                       
                    continue 2;
            }            
        }
        $k++;  
    }         
}
