<?php
function apiRequest($url, $apiKey, $query, $accept)
{
    $queryUrl = http_build_query($query);//Преобразовние массива к url строке
    
    $ch = curl_init($url);

    //Внесение header params в api запрос
    curl_setopt(
        $ch, 
        CURLOPT_HTTPHEADER, 
        array(
            "X-ELS-APIKey: $apiKey",
            "Accept: $accept"
        )
    );  
    curl_setopt($ch, CURLOPT_POSTFIELDS, $queryUrl); //Внесение query params в api запрос
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch); //Выполнение запроса
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch); //Закрытие curl сеанса

    if ($http_code == 200){
        // тут какая-то обработка текста страницы
        $p = xml_parser_create();
        xml_parse_into_struct($p, $response, $result, $index);
        xml_parser_free($p);
        return $result;
    } else {
        return $http_code;
    }
}

function apiRequestPDF($url, $apiKey, $query, $accept)
{
    $queryUrl = http_build_query($query);//Преобразовние массива к url строке
    
    $ch = curl_init($url);

    //Внесение header params в api запрос
    curl_setopt(
        $ch, 
        CURLOPT_HTTPHEADER, 
        array(
            "X-ELS-APIKey: $apiKey",
            "Accept: $accept"
        )
    );  
    curl_setopt($ch, CURLOPT_POSTFIELDS, $queryUrl); //Внесение query params в api запрос
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch); //Выполнение запроса
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch); //Закрытие curl сеанса

    if ($http_code == 200){
        return $response;
    } else {
        return $http_code;
    }
}
