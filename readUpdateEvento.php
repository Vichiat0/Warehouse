<?php
    session_start();
    include_once("Conexao_BD.php");

    //Recebe dados do usuário
    $usuario = $_SESSION['usuario'];
    $queryPesquisa = "SELECT * FROM usuario WHERE email = '$usuario' LIMIT 1";
    $result = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
    $retorno = array('erro' => 'nenhum',
                     'msg' => '');

    $mesAnoAtual = $_POST['Itsmesano'];
    $ultimoDiaMes = $_POST['Itsdiasmes'];

    switch($_POST['Itsoperacao']){
        //Recebe os eventos do BD armazenando-os 
        case "read":
            if(empty($result)){ 
                $retorno["erro"] = "usuario";
                $_SESSION['usuario'] = null;
                $_SESSION['erroHome'] = "Não foi possível acessar os dados do usuário, tente reconectar-se. <br>
                                         Código do erro: " . mysqli_errno($conn);
                echo json_encode($retorno);
                die();
            }
            else{
                $cpfcnpj = $result['cpfcnpj'];
            }
            $eventoArray = array();
            for($verificaDia = 1; $verificaDia <= $ultimoDiaMes; $verificaDia++){
                $evento = $mesAnoAtual;
                if($verificaDia < 10){
                    $evento = $evento . "0";
                }
                $evento = $evento . $verificaDia;
                $queryPesquisa = "SELECT * FROM eventostransporte WHERE pessoaId = '$cpfcnpj' AND dataId='$evento'";
                $result = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
                if(!empty($result)){
                    array_push($eventoArray,array("id" => $result['dataId'],
                                                  "start" => $result['dataId'],
                                                  "color" => 'light blue',
                                                  "allDay" => true));
                }
                for($verificaHorario = 1; $verificaHorario <= 5; $verificaHorario++){
                    $queryPesquisa = "SELECT * FROM eventostransporte WHERE pessoaId = '$cpfcnpj' AND dataId='" . $evento . "_" . $verificaHorario . "'";
                    $result = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
                    if(!empty($result)){
                        $dataSeparada = explode('_',$result['dataId']);
                        $horario1 = substr_replace($result['horario1'],"",-3,3);
                        $horario2 = substr_replace($result['horario2'],"",-3,3);
                        $inicio = array_shift($dataSeparada) . "T" . $horario1;
                        $dataSeparada = explode('_',$result['dataId']);
                        $fim = array_shift($dataSeparada) . "T" . $horario2;
                        array_push($eventoArray,array("id" => $result['dataId'],
                                                      "title" => $horario1 . "-" . $horario2,
                                                      "start" => $inicio,
                                                      "end" => $fim,
                                                      "color" => 'orange',
                                                      "allDay" => true));
                    }
                }
            }
            echo json_encode($eventoArray);
            die();
        break;

        //Atualiza os eventos no BD
        case "update":
        
            if(empty($result)){ 
                $retorno['erro'] = "usuario";
                $_SESSION['usuario'] = null;
                $_SESSION['erroHome'] = "Não foi possível acessar os dados do usuário, tente reconectar-se. <br>
                                         Código do erro: " . mysqli_errno($conn);
                echo json_encode($retorno);
                die();
            }
            else{
                $cpfcnpj = $result['cpfcnpj'];
            }

            //Deleta os eventos do usuário já registrados
            if(!empty(mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM eventostransporte WHERE pessoaId = '$cpfcnpj' LIMIT 1")))){
                if(empty(mysqli_query($conn, "DELETE FROM eventostransporte WHERE pessoaId = '$cpfcnpj'"))){
                    $retorno['erro'] = "evento";
                    $retorno['msg'] = "Não foi possível atualizar os eventos de transportador do usuário, tente novamente. <br>
                                       Código do erro: " . mysqli_errno($conn);
                    echo json_encode($retorno);
                    die();
                }
            }

            if($_POST['Itstransportador'] == "S"){
                //Insere-os novamente já atualizados
                $queryInserir = "INSERT INTO eventostransporte (pessoaId,dataId,horario1,horario2) VALUES ";
                for($verificaDia = 1; $verificaDia <= $ultimoDiaMes; $verificaDia++){
                    if($verificaDia < 10){
                        $diaFormatado = "0".$verificaDia;
                    }
                    else{
                        $diaFormatado = $verificaDia;
                    }
                    if(!empty($_POST[$diaFormatado."_id"]) && $_POST[$diaFormatado."_id"] != "null"){
                        $dataId = $diaFormatado."_id";
                        $queryInserir = $queryInserir . "($cpfcnpj,'" . $_POST[$dataId] . "','',''),"; 
                    }
                    else{
                        for($verificaHorario = 1; $verificaHorario <= 5; $verificaHorario++){
                            if(!empty($_POST[$diaFormatado."_".$verificaHorario."_id"]) && $_POST[$diaFormatado."_".$verificaHorario."_id"] != "null"){
                                $dataId = $diaFormatado."_".$verificaHorario."_id";
                                $horario1 = $diaFormatado."_".$verificaHorario."_horario1";
                                $horario2 = $diaFormatado."_".$verificaHorario."_horario2";
                                $queryInserir = $queryInserir . "('$cpfcnpj','" . 
                                                                  $_POST[$dataId] . "','" . 
                                                                  $_POST[$horario1] . "','" .
                                                                  $_POST[$horario2] . "'),"; 
                            }
                        }
                    }
                }

                //Verifica se a pessoa deixou o calendário sem eventos
                if($queryInserir != "INSERT INTO eventostransporte (pessoaId,dataId,horario1,horario2) VALUES "){
                    $queryInserir = substr_replace($queryInserir,"",-1);
                    if(empty(mysqli_query($conn,$queryInserir))){
                        $retorno['erro'] = "evento";
                        $retorno['msg'] = "Não foi possível atualizar os eventos de transportador do usuário, tente novamente. <br>
                                           Código do erro: " . mysqli_error($conn);
                        echo json_encode($retorno);
                        die();
                    }
                }
            }
            else{
                mysqli_query($conn, "DELETE FROM eventostransporte WHERE pessoaId = '$cpfcnpj'");
            }
            echo json_encode($retorno);
        break;
    }
?>