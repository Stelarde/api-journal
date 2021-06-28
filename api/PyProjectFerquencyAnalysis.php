<?php

function PyProjectFA($id)
{
    $url = "api-service:5001/ai-quotes/$id";

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    $response = curl_exec($ch); //Выполнение запроса
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch); //Закрытие curl сеанса

    return $response;    
}