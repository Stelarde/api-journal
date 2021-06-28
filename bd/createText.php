<?php
function createText($vals)
{
    global $wpdb;
    $text;
    $scopusId;  
    $user_id = get_current_user_id();
    if ($vals){
        foreach($vals as $value) {    
            switch ($value["tag"]) {
                case "SERVICE-ERROR":
                    break;
                case "SCOPUS-ID":
                    $scopusId = $value["value"];
                    $articleId = $wpdb->get_results("SELECT `id` 
                        FROM `wp_article` 
                        WHERE `scopus_id` = '$scopusId'
                        AND `user_id` = '$user_id'", ARRAY_A); 
                    continue 2;
                case "CE:LABEL":
                    $label = trim($value["value"]);
                    $bool = true;
                    continue 2;
                case "CE:SECTION-TITLE":   
                    if (isset($value["value"])){
                        if ($value["value"] != "References"){
                            $checkIdArticle = $articleId[0]['id'];
                            $checkString = $label . ' ' . trim($value["value"]);
                            $check = $wpdb->get_results("SELECT wp_article_section_title.id, wp_article_section_title.name 
                                FROM wp_article_section_title 
                                WHERE wp_article_section_title.name = '$checkString' AND wp_article_section_title.wp_article_id = '$checkIdArticle'", ARRAY_A);
                            if (isset($check[0])){
                                break 2;
                            } else {
                                if ($bool){
                                    $wpdb->insert('wp_article_section_title',
                                    array('name' => $label . ' ' . trim($value["value"]), 'wp_article_id' => $articleId[0]['id']),
                                    array('%s', '%d')
                                    );
                                    $bool = false;
                                } else {
                                    $wpdb->insert('wp_article_section_title',
                                    array('name' => trim($value["value"]), 'wp_article_id' => $articleId[0]['id']),
                                    array('%s', '%d')
                                    );
                                }
                                $idTitle = $wpdb->insert_id;
                            } 
                        }                
                    }  
                    continue 2;                  
                case "CE:TEXT":
                    if (isset($value["value"])){
                        if ($value["value"] != "Corresponding author."){
                            if (isset($text)){
                                $text = $text . trim($value["value"]);
                            } else {
                                $text = trim($value["value"]);
                            }
                        }  
                    }                        
                    continue 2;
                case "CE:PARA":     
                    if (isset($value["value"])){
                        if (isset($text)){
                            $text = $text . trim($value["value"]);
                        } else {
                            $text = trim($value["value"]);
                        }
                    }
                    continue 2;
                case "HEAD":
                    if ($value["type"] == "close"){
                        if(isset($idTitle)){
                            $wpdb->insert('wp_article_section_text',
                            array('text' => ! isset($text) ? $text : '', 
                            'article_title_id' => $idTitle),
                            array('%s', '%d')
                            );                            
                        }  
                        unset($text);
                        $text;                      
                    }
                    continue 2;
                case "CE:SECTION":
                    if ($value["type"] == "close"){
                        $wpdb->insert('wp_article_section_text',
                        array('text' => isset($text) ? $text : '', 
                        'article_title_id' => $idTitle),
                        array('%s', '%d')
                        );
                        unset($text);
                        $text;
                    }
                    continue 2;
                case "BODY":
                    if ($value["type"] == "close"){
                        $wpdb->insert('wp_article_section_text',
                        array('text' => isset($text) ? $text : '', 
                        'article_title_id' => $idTitle),
                        array('%s', '%d')
                        );
                        unset($text);
                        $text;
                    }
                    continue 2;
            }
        }
    }
}