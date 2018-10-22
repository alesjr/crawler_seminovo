<?php

// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);
$app->get("/buscar-informacoes", function() {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, "https://www.seminovosbh.com.br");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $dados = curl_exec($ch);
    curl_close($ch);
});
