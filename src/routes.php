<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes
$app->get('/', function (Request $request, Response $response, array $args) use ($app) {
    $ch = curl_init();
    
    $veiculos = array("carro", "moto", "caminhao");
    foreach($veiculos as $cada_veiculo){
        $last_page = null;
        $page = 1;
        
        do{

            $db = ConexaoPDO::getConexaoPDO($app);

            curl_setopt($ch, CURLOPT_URL, "https://www.seminovosbh.com.br/resultadobusca/index/veiculo/{$cada_veiculo}/valor2/2000000/ano1/1930/ano2/2019/usuario/todos/pagina/{$page}");
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $body = curl_exec($ch);

            //pagina com erro
            if(curl_errno($ch) > 0){
                $page++;
                continue;
            }

            curl_close($ch);

            //fazendo o preg replace callback para poder pegar exatamente o ponto onde eu quero através dos próprios callbacks
            //passo para dentro do callback uma variável de conexao de bd por que a mesma não é acessível dentro desta função
            preg_replace_callback( "/<a.*href=\"\/comprar\/([^\"]*)\"\s*>.*<span.*>(.*)<\/span>.*<\/a>/", function ($matches) use ($db){

                //tratando os dados dos quais são retornados pelo array match do regex com parenteses
                $veiculo_url = (int) $matches[1];
                $dados_veiculo = preg_split('@/@', $matches[1], NULL, PREG_SPLIT_NO_EMPTY);;

                $marca = ucfirst($dados_veiculo[0]);
                $modelo = ucfirst($dados_veiculo[1]);

                $anos = explode("-", $dados_veiculo[2]);
                $ano_inicial = $anos[0];
                $ano_final = $anos[1] ? $anos[1] : $anos[0];

                $veiculo_id = $dados_veiculo[3];
                $veiculo_valor = floatval(preg_replace("/[^0-9,]/", "", $matches[2]));

                try{
                    $db->beginTransaction();

                    $sql_veiculo = "REPLACE INTO veiculo values (:veiculo_id, :veiculo_marca, :veiculo_modelo, :veiculo_url, :veiculo_valor, :veiculo_ano_inicial, :veiculo_ano_final)";
                    $sth_veiculo = $db->prepare($sql_veiculo);
                    $sth_veiculo->bindParam("veiculo_id", $veiculo_id);
                    $sth_veiculo->bindParam("veiculo_marca", $marca);
                    $sth_veiculo->bindParam("veiculo_modelo", $modelo);
                    $sth_veiculo->bindParam("veiculo_url", $veiculo_url);
                    $sth_veiculo->bindParam("veiculo_valor", $veiculo_valor);
                    $sth_veiculo->bindParam("veiculo_ano_inicial", $ano_inicial);
                    $sth_veiculo->bindParam("veiculo_ano_final", $ano_final);
                    $sth_veiculo->execute();

                    $db->commit();

                } catch (\Exception $ex){
                    $db->rollBack();
                    //lugar para gravar o log
                }

                return "";
            }, $body);

            if(!$last_page){

                preg_replace_callback("/<strong.*\"total\".*>(.*)<\/strong>/", function($matches) use (&$last_page) {
                    $last_page = (int) $matches[1];
                    return "";
                }, $body);
            }
            $page++;
        } while($page < $last_page);
    }
    // Render index view
    return $response->getStatusCode();
});
