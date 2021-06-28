<?php
function createSerial($data)
{
    global $wpdb;
    $wpdb->query( " DELETE FROM `wp_journal` WHERE 1 = 1; " );
    foreach($data as $vals) {
        foreach ($vals as $value){
            if ($value["tag"] == "DC:TITLE"){
                $name = trim($value["value"]);
            }
            if (isset($value["attributes"]["REF"])) {
                if ($value["attributes"]["REF"] == "coverimage"){
                    $url = trim($value["attributes"]["HREF"]);        
                    $wpdb->insert('wp_journal', 
                        array('name' => $name, 'url' => $url),
                        array('%s', '%s')
                    );
                }
            }           
        }        
    }
}
