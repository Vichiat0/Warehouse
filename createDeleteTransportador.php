<?php 
    include_once('Conexao_BD.php');
    switch($_POST['operacao']){
        case "create":
            $pessoaCpfCnpj = $_POST['pessoaCpfcnpj'];
            $queryPesquisa = "SELECT cpfcnpj, nome, fotoTipo, fotoPerfil, modeloCarro, placaCarro, corCarro FROM usuario, eventostransporte WHERE cpfcnpj=pessoaId AND transportador='S'AND ((dataId LIKE '%" . $_POST['dataTransporte'] . "%' AND CAST('" . $_POST['horarioTransporte'] . "' AS TIME) >= horario1 AND CAST('" . $_POST['horarioTransporte'] . "' AS TIME) <= horario2) OR dataId='" . $_POST['dataTransporte'] . "')  LIMIT 1";
            $resultTransportador = mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa));
            if(!empty($resultTransportador)){
                $retorno = array("nome" => $resultTransportador['nome'],
                                 "fotoPerfil" => "",
                                 "fotoTipo" => $resultTransportador['fotoTipo'],
                                 "modeloCarro" => $resultTransportador['modeloCarro'],
                                 "placaCarro" => $resultTransportador['placaCarro'],
                                 "corCarro" => $resultTransportador['corCarro']);
                if($retorno['fotoTipo'] == "site"){
                    $retorno['fotoPerfil'] = "style/media/usericon" . bindec($resultTransportador['fotoPerfil']) . ".png";
                }
                else{
                    $retorno['fotoPerfil'] = "data:" . $resultTransportador['fotoTipo'] . ";base64," . base64_encode($resultTransportador['fotoPerfil']);
                }
                $queryInserir = "UPDATE transacao SET transportador='" . $resultTransportador['cpfcnpj'] . "', dataEntrega='" . $_POST['dataCompleta'] . "' WHERE itemPrimario=" . $_POST['transacao'];
                mysqli_query($conn,$queryInserir);
                $queryInserir = "INSERT INTO notificacao (id,cpfcnpj,tipo) VALUES ('" . $_POST['transacao'] . "','" . $resultTransportador['cpfcnpj'] . "', 'transporte')";
                mysqli_query($conn,$queryInserir);
            }
            else{
                $retorno = array("nome" => null);
            }
            echo json_encode($retorno);
        break;
        case "delete":
            switch($_POST['quemDeletou']){
                case "negociadores":
                    $pessoaCpfCnpj = $_POST['pessoaCpfcnpj'];
                    $queryPesquisa = "SELECT transportador, cpfcnpj1, cpfcnpj2 FROM transacao WHERE itemPrimario=" . $_POST['transacao'];
                    $resultTransacao = mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa));
                    $queryUpdate = "UPDATE transacao SET transportador=null, dataEntrega=null WHERE itemPrimario=" . $_POST['transacao'];
                    mysqli_query($conn,$queryUpdate);
                    $queryDelete = "DELETE FROM notificacao WHERE id=" . $_POST['transacao'] . " AND cpfcnpj='" . $resultTransacao['transportador'] . "'";
                    mysqli_query($conn,$queryDelete);
                    if(empty(mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM notificacao WHERE id=" . $_POST['transacao'] . " AND cpfcnpj='" . $resultTransacao['transportador'] . "'")))){
                        $queryInserir = "INSERT INTO notificacao (id,cpfcnpj,tipo,remetente) VALUES (" . $_POST['transacao'] . ",'" . $resultTransacao['transportador'] . "', 'cancelaTransporte', '" . $resultTransacao[$pessoaCpfCnpj] . "')";
                        mysqli_query($conn,$queryInserir);
                        if($pessoaCpfCnpj == "cpfcnpj1"){
                            $queryInserir = "INSERT INTO notificacao (id,cpfcnpj,tipo,remetente) VALUES (" . $_POST['transacao'] . ",'" . $resultTransacao['cpfcnpj2'] . "', 'cancelaTransporte', '" . $resultTransacao['cpfcnpj1'] . "')";
                            mysqli_query($conn,$queryInserir);
                        }
                        else{
                            $queryInserir = "INSERT INTO notificacao (id,cpfcnpj,tipo,remetente) VALUES (" . $_POST['transacao'] . ",'" . $resultTransacao['cpfcnpj1'] . "', 'cancelaTransporte', '" . $resultTransacao['cpfcnpj2'] . "')";
                            mysqli_query($conn,$queryInserir);
                        }
                    }
                break;
                case "transportador":
                    $queryPesquisa = "SELECT transportador, cpfcnpj1, cpfcnpj2 FROM transacao WHERE itemPrimario=" . $_POST['transacao'];
                    $resultTransacao = mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa));
                    $queryInserir = "INSERT INTO notificacao (id,cpfcnpj,tipo,remetente) VALUES (" . $_POST['transacao'] . ",'" . $resultTransacao['cpfcnpj1'] . "', 'cancelaTransporte', '" . $resultTransacao['transportador'] . "')";
                    mysqli_query($conn,$queryInserir);
                    $queryInserir = "INSERT INTO notificacao (id,cpfcnpj,tipo,remetente) VALUES (" . $_POST['transacao'] . ",'" . $resultTransacao['cpfcnpj2'] . "', 'cancelaTransporte', '" . $resultTransacao['transportador'] . "')";
                    mysqli_query($conn,$queryInserir);
                break;
            }
        break;
    }
?>