<?php
    function apiMultiRequest($inputParams){
        $chs = array();
        $result = array();
        $k = 0;
        $n = 0;

        $mh = curl_multi_init();
        foreach($inputParams as $value){        
            $chs[] = ($ch = curl_init($value['url']));
            $apiKey = $value['apiKey'];
            $accept = $value['accept'];
            curl_setopt(
                $ch, 
                CURLOPT_HTTPHEADER, 
                array(
                    "X-ELS-APIKey: $apiKey",
                    "Accept: $accept"
                )
            ); 
            curl_setopt($ch, CURLOPT_POSTFIELDS, $value['queryUrl']); //Внесение query params в api запрос

            // CURLOPT_RETURNTRANSFER - возвращать значение как результат функции, а не выводить в stdout    
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_multi_add_handle($mh, $ch);
            $k++;
        }

        $prev_running = $running = null;
        
        do {
            curl_multi_exec($mh, $running);
            
            if ($running != $prev_running){
                // получаю информацию о текущих соединениях    
                $info = curl_multi_info_read($mh);
                
                if (is_array($info) && ($ch = $info['handle'])) {
    
                    // получаю содержимое загруженной страницы
                    $response = curl_multi_getcontent($ch);
                    
                    // тут какая-то обработка текста страницы
                    $p = xml_parser_create();
                    xml_parse_into_struct($p, $response, $vals, $index);
                    xml_parser_free($p);
                    $result[$n] = $vals;
                    $n++;
                }           
                // обновляю кешируемое число текущих активных соединений
                $prev_running = $running;
            }
            
        } while ($running > 0);

        foreach ($chs as $ch) {
            curl_multi_remove_handle($mh, $ch);
            curl_close( $ch );    
        }
        curl_multi_close($mh);     
        return $result;
    }
