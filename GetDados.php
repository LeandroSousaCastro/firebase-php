<?php

require __DIR__ . '/PcdModel/Pcd.php';

$authorization = "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6Ijc1Mzk5OGQyMjQxNjE0MTE0ZGYyYjQ5ODM4ZDAxMDgyM2M3N2YzODEyMWFlNTdlZmU0MzYwZWE5MWYyMWE2OGM0MDBmNGY3NDQwMGI5Zjk1In0.eyJhdWQiOiI1IiwianRpIjoiNzUzOTk4ZDIyNDE2MTQxMTRkZjJiNDk4MzhkMDEwODIzYzc3ZjM4MTIxYWU1N2VmZTQzNjBlYTkxZjIxYTY4YzQwMGY0Zjc0NDAwYjlmOTUiLCJpYXQiOjE1NDAzMTQ1OTIsIm5iZiI6MTU0MDMxNDU5MiwiZXhwIjoxNTcxODUwNTkxLCJzdWIiOiIiLCJzY29wZXMiOltdfQ.rkMHkATiwCxEkO0w6FGYyX-RjyyW2eq0fbCslFf1cBplJgG3VAhhrsd39WXcEOItgcQ26prWIjOqyvsTSZKfeKDRVkn4oaVW2I93sbQqcs0dQmG3VoI-8nnrNlslid0RrXKajesVa4pA1zL5Dpy-jFY74u5u2OF4v7T3cZLvDA7eajtMBry3Khp4souiNEksaYXfQLPA38mUh9BvjHrQhRkNJKO-NqYab9VDs63sO1Eafla7itkCzaniNoJVZ4NT5-8SP2kwmAcV3CTZAaQARbAaYqpEPBp4qYw_EwLTSLfjNZ7iSUKkeL1WA9daiaTAqBB54cPf921_aj20ozakk-B8YCjE9RgODn--V1fPj_qe2xunmkhy-8NzwxdJkdPHG66S-QQaqIbv2DXswvy61BPF5Kr-kTv8K-J628bEiKFLr1dLWAixGldSTi5DGEA0_bzEwTLNILjptQ9iRgdfbuyvwnevaIbYVh8YCdZfabsu5Pl7P4jicNo__STSSyTKYvraSM-i8Z-HzRNl7R2yXWkHufVHCh9nfXeW-IWd7v9Tj1-5l3QSMBi2AaPPJ5a7CiwGUv-5A4uPWrxoFzoj7coRFGEJjfRw6PrirVaKoWGO3hBJ6o4FsLkwrnk-JtYr8ZkW75ZDAf1m5bQ6QPxJBRZtdQTPWzQgeKJdfgWP30U";
//$urlEstacoes = "http://apil5.funceme.br/rest/pcd/estacao?instituicao=1&tipo_estacao=3&modelo=1&codigo_origem-lk=B&limit=14";
$urlEstacoes = "http://apil5.funceme.br/rest/pcd/estacao?instituicao=41&with=municipio.uf&municipio-uf=CE&limit=26";

$curl = curl_init();
curl_setopt($curl, CURLOPT_HTTPHEADER, [$authorization, 'Content-Type: application/json']);
curl_setopt($curl, CURLOPT_URL, $urlEstacoes);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPGET, true);
$estacoes = \json_decode(curl_exec($curl));
curl_close($curl);

$pcd = new Pcd();
foreach ($estacoes->data->list as $estacao) {
    $nome = $estacao->nome . " - " . $estacao->municipio->uf . " ( " . $estacao->id . " - " . $estacao->instituicao->nome . " )";
    $url = "http://apil5.funceme.br/rest/pcd/dado-sensor?estacao=$estacao->id&data-GTE=2019-03-21%2000:00&data-LTE=2019-03-21%2023:59&sensor=2&orderBy=data,DESC&limit=24";
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_HTTPHEADER, [$authorization, 'Content-Type: application/json']);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HTTPGET, true);
    $result = curl_exec($curl);
    curl_close($curl);
    $dados_pcds = \json_decode($result);

    $result = [];
    if ($dados_pcds->data->total_results > 0) {
        $list = [];

        $umahora = null;
        $duashora = null;
        $seishora = null;
        $dozehora = null;
        $vinteQuatroHora = null;
        $i = 1;
        foreach ($dados_pcds->data->list as $dado_pcd) {
            if ($i == 1) {
                $umahora = (float)$dado_pcd->valor;
            } else if ($i > 1 and $i <= 2) {
                $duashora += (float)$dado_pcd->valor;
            } else if ($i > 2 and $i <= 6) {
                $seishora += (float)$dado_pcd->valor;
            } else if ($i > 6 and $i <= 12) {
                $dozehora += (float)$dado_pcd->valor;
            } else if ($i > 12 and $i <= 24) {
                $dozehora += (float)$dado_pcd->valor;
            }
            $i++;
        }

        $duashora = ($duashora + $umahora) / 2;
        $seishora = ($seishora + $duashora) / 6;
        $dozehora = ($dozehora + $seishora) / 12;
        $vinteQuatroHora = ($vinteQuatroHora + $dozehora) / 24;

        $result = $pcd->update([
            $estacao->id => [
                'nome' => $nome,
                'dados' => [
                    1 => round($umahora, 1),
                    2 => round($duashora, 2),
                    6 => round($seishora, 2),
                    12 => round($dozehora, 2),
                    24 => round($vinteQuatroHora, 2),
                ]
            ]
        ]);
        var_dump($result);exit;
    }
}
