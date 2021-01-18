<?php
    session_start();
    include_once("Conexao_BD.php");

    //Prepara o retorno
    $retorno = array("paginas" => 0,
                     "resultado" => 'false',
                     'erro' => 'nenhum',
                     'msg' => '');  

    $resultUsuario = mysqli_fetch_assoc(mysqli_query($conn,"SELECT cpfcnpj FROM usuario WHERE email='" . $_SESSION['usuario'] . "'"));

    switch($_POST['operacao']){
        case 'read':
            switch($_POST['tipoPesquisa']){

                //Pesquisa um item individual
                case 'item':
                    $queryPesquisa = "SELECT * FROM item WHERE id=" . $_POST['id'];
                    $resultItem = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
                    if(empty($resultItem)){
                        $retorno['erro'] = "item";
                        $retorno['msg'] = "Não foi possível acessar os dados do item.<br>
                                           Código de erro: " . mysqli_errno($conn);
                        echo json_encode($retorno);
                        die();
                    }
                    $retorno['intuito'] = $resultItem['intuito'];
                    $retorno['nome'] = $resultItem['nome'];
                    $retorno['categoria'] = $resultItem['categoria'];
                    $retorno['imagem'] = "data:" . $resultItem['imagemTipo'] . ";base64," . base64_encode($resultItem['imagem']);
                    $retorno['descricao'] = $resultItem['info'];
                break;

                //Pesquisa os itens do usuário em questão
                case 'usuario':
                    $queryPesquisa = "SELECT cpfcnpj FROM usuario WHERE email='" . $_POST['usuario'] . "'";
                    $resultCpfcnpj = mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa));

                    $queryPesquisa = "SELECT * FROM item WHERE cpfcnpj='" . $resultCpfcnpj['cpfcnpj'] . "' AND emUso='n' LIMIT " . ($_POST['indicePesquisa'] - 1)  *  20 . ",20";
                    //Armazena os resultados da pesquisa e envia de volta
                    if(!empty(mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa)))){
                        $retorno['resultado'] = 'true';
                        $resultQuery = mysqli_query($conn,$queryPesquisa);
                        while($resultItens = mysqli_fetch_assoc($resultQuery)){
                            array_push($retorno,array("id" => $resultItens['id'],
                                                      "nome" => $resultItens['nome'],
                                                      "categoria" => $resultItens['categoria'],
                                                      "intuito" => $resultItens['intuito'],
                                                      "info" => $resultItens['info'],
                                                      "imagem" => base64_encode($resultItens['imagem']),
                                                      "imagemTipo" => $resultItens['imagemTipo']));
                        }
                    }
                break;

                //Faz pesquisa generalizada nos itens
                case 'geral':
                    $stringDividida = explode(" ",$_POST["stringChave"]);
                    $arrayPalavrasSeparadas = array($stringDividida);
                    $orderId = 1;
                    $queryOrdenacao = "";

                    //Pesquisa pelas palavras-chave isoladamente
                    foreach($stringDividida as $palavra){
                        $queryOrdenacao = $queryOrdenacao . "(nome LIKE '%" . $palavra . "%') OR ";
                    }
                    $queryOrdenacao = substr_replace($queryOrdenacao,"",-3,2);

                    //Adiciona categoria
                    if($_POST['categoria'] != ""){
                        $queryOrdenacao = $queryOrdenacao . "AND categoria='" . $_POST['categoria'] . "'";
                    }

                    //Adiciona intuito
                    if($_POST['intuito'] != ""){
                        if($_POST['intuito'] == "Troca"){
                            $queryOrdenacao = $queryOrdenacao . "AND intuito='troca'";
                        }
                        else{
                            $queryOrdenacao = $queryOrdenacao . "AND intuito='doacao'";
                        }
                    }
    
                    //Ordena o resultado por relevância das palavras (começando da frase inteira até palavras individuais)
                    //Ex:
                    //  Pesquisa: "Computador bem grande"
                    //  Palavras-chave: "Computador"; "Bem"; "Grande"
                    //  Ordenação dos resultados:
                    //      Prioridade 1: "Computador bem grande";
                    //      Prioridade 2: "Computador bem";
                    //      Prioridade 3: "Computador";
                    //      Prioridade 4: "bem grande";
                    //      Prioridade 5: "bem";
                    //      Prioridade 6: "grande";    
                    $queryOrdenacao = $queryOrdenacao . "AND cpfcnpj != '" . $resultUsuario['cpfcnpj'] . "' AND emUso='n' 
                                                         ORDER BY( 
                                                            CASE";                  
                    for($indicePalavra = 0; $indicePalavra < count($stringDividida); $indicePalavra++){
                        for($numPalavrasSeparadas = count($stringDividida); $numPalavrasSeparadas > $indicePalavra; $numPalavrasSeparadas--){
                            $queryOrdenacao = $queryOrdenacao . " WHEN nome LIKE '%";
                            for($contArray = $indicePalavra; $contArray < $numPalavrasSeparadas; $contArray++){
                                $queryOrdenacao = $queryOrdenacao . $stringDividida[$contArray] . " ";
                            }
                            $queryOrdenacao = substr_replace($queryOrdenacao,"",-1);
                            $queryOrdenacao = $queryOrdenacao . "%' THEN $orderId";
                            $orderId++;
                        }
                    }

                    //Verifica se o usuário fez uma nova pesquisa
                    if($_POST['novaPesquisa'] == 's'){
                        $queryPesquisa = "SELECT COUNT(*) FROM item WHERE " . $queryOrdenacao . " END)";
                        $resultContagem = mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa));
                        $retorno['paginas'] = ceil($resultContagem['COUNT(*)'] / 20);
                    }

                    //Coloca o índice da pesquisa para separação de páginas
                    $queryOrdenacao = $queryOrdenacao . " END) LIMIT " . ($_POST['indicePesquisa'] - 1)  *  20 . ",20";

                    //Conclui a query
                    $queryPesquisa = "SELECT * FROM item WHERE " . $queryOrdenacao;

                    //Armazena os resultados da pesquisa e envia de volta
                    if(!empty(mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa)))){
                        $retorno['resultado'] = 'true';
                        $resultQuery = mysqli_query($conn,$queryPesquisa);
                        while($resultItens = mysqli_fetch_assoc($resultQuery)){
                            array_push($retorno,array("id" => $resultItens['id'],
                                                      "nome" => $resultItens['nome'],
                                                      "categoria" => $resultItens['categoria'],
                                                      "intuito" => $resultItens['intuito'],
                                                      "info" => $resultItens['info'],
                                                      "imagem" => base64_encode($resultItens['imagem']),
                                                      "imagemTipo" => $resultItens['imagemTipo']));
                        }
                    }
                break;
            }
        break;
        case 'delete':
            $queryDelete = "DELETE FROM item WHERE id=" . $_POST['id'];
            if(empty(mysqli_query($conn, $queryDelete))){
                $retorno['erro'] = "item";
                $retorno['msg'] = "Erro ao tentar excluir o item.<br>
                                   Código de erro: " . mysqli_errno($conn);
                echo json_encode($retorno);
                die();
            }
            $usuario = $_SESSION['usuario'];
            $queryPesquisa = "SELECT * FROM usuario WHERE email = '$usuario' LIMIT 1";
            $resultUsuario = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
            if(empty($resultUsuario)){
                $retorno['erro'] = "usuario";
                $_SESSION['erroHome'] = "Não foi possível acessar os dados do usuário.<br>
                                         Código de erro: " . mysqli_errno($conn);
                $_SESSION['usuario'] = null;
                echo json_encode($retorno);
                die();
            }
            $queryPesquisa = "SELECT * FROM item WHERE cpfcnpj=" . $resultUsuario['cpfcnpj'];
            $resultItem = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
            if(empty($resultItem)){
                $retorno['vazio'] = "s";
            }
            else{
                $retorno['vazio'] = "n";
            }
        break;
        case "update":
            switch($_POST['tipoUpdate']){
                case "geral":
                    $queryPesquisa = "SELECT * FROM item WHERE id=" . $_POST['id'];
                    $resultItem = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
                    if(empty($resultItem)){
                        $retorno['erro'] = "item";
                        $retorno['msg'] = "Não foi possível acessar os dados do item.<br>
                                           Código de erro: " . mysqli_errno($conn);
                        echo json_encode($retorno);
                        die();
                    }

                    $arquivo = "";
                    $fotoTamanho = "";
                    $fotoRecebida = "";
                    if(!empty($_FILES['imagemArquivo'])){
                        $fotoRecebida = "usuario";
                        //Nome do arquivo
                        $fotoNome = $_FILES['imagemArquivo']['name'];
                        //Tamanho do arquivo em Bytes
                        $fotoTamanho = $_FILES['imagemArquivo']['size'];
                        //Extensão do arquivo
                        $fotoTipo = $_FILES['imagemArquivo']['type'];
                        //Nome pelo qual o arquivo é reconhecido pelo código
                        $arquivo = $_FILES['imagemArquivo']['tmp_name'];
                    }
                    else{
                        $fotoTipo = $resultItem['imagemTipo'];
                        $fotoItem = addslashes($resultItem['imagem']);
                    }

                    include_once("verificaCampos.php");
                    $retorno['msg'] = validaCamposEstoque($_POST['nome'],$_POST['categoria'],$_POST['descricao'],$arquivo,$fotoTipo,$fotoTamanho,$fotoRecebida);
                    if($retorno['msg'] != ""){
                        $retorno['erro'] = "item";
                        echo json_encode($retorno);
                        die();
                    }

                    if(!empty($_FILES['imagemArquivo'])){
                        //Abre o arquivo de imagem em formato rb (Read, com o parametro b para arquivos não textuais)
                        $fotoAberta = fopen($arquivo,"rb");

                        //Extrai o conteúdo do arquivo (fread(arquivoAberto,atéQueByteDeveExtrair))
                        //Já que devemos extrair toda a imagem, a largura da extração é o tamanho máximo do arquivo
                        //addslashes = Função que adiciona \ antes de caracteres especiais para sua leitura
                        $fotoItem = addslashes(fread($fotoAberta,$fotoTamanho));

                        //Fecha arquivo
                        fclose($fotoAberta);
                    }
            

                    $queryUpdate = "UPDATE item SET ";
                    if($_POST['nome'] != $resultItem['nome']){
                        $queryUpdate = $queryUpdate . "nome='" . $_POST['nome'] . "',";
                    }
                    if($_POST['categoria'] != $resultItem['categoria']){
                        $queryUpdate = $queryUpdate . "categoria='" . $_POST['categoria'] . "',";
                    }
                    if($_POST['descricao'] != $resultItem['info']){
                        $queryUpdate = $queryUpdate . "info='" . $_POST['descricao'] . "',";
                    }
                    if($_POST['intuito'] != $resultItem['intuito']){
                        $queryUpdate = $queryUpdate . "intuito='" . $_POST['intuito'] . "',";
                    }
                    if($fotoTipo != $resultItem['imagemTipo']){
                        $queryUpdate = $queryUpdate . "imagemTipo='$fotoTipo',";
                    }
                    if($fotoItem != addslashes($resultItem['imagem'])){
                        $queryUpdate = $queryUpdate . "imagem='$fotoItem',";
                    }

                    $queryUpdate = substr_replace($queryUpdate,"",-1);
                    $queryUpdate = $queryUpdate . " WHERE id=" . $_POST['id'];

                    if($queryUpdate != "UPDATE item SET WHERE id=" . $_POST['id']){
                        if(empty(mysqli_query($conn,$queryUpdate))){
                            //DEU ERRO MERMÃO
                            $retorno['msg'] = "Erro ao tentar atualizar os dados do item.<br> 
                                               Código de erro: " . mysqli_error($conn) . " " . $queryUpdate;
                            $retorno['erro'] = "item";
                            echo json_encode($retorno);
                            die();
                        }
                    }
                break;
                case "uso":
                    if($_POST["itemAtual"] != ""){
                        $queryUpdate = "UPDATE item SET emUso='n' WHERE id=" . $_POST['itemAtual'];
                        mysqli_query($conn,$queryUpdate);
                    }
                    $queryUpdate = "UPDATE item SET emUso='s' WHERE id=" . $_POST['itemNovo'];
                    mysqli_query($conn,$queryUpdate);
                break;
            }
        break;
    }
    echo json_encode($retorno);
    



?>