<?php
    session_start(); 
    //Incluindo a conexão com banco de dados   
    include_once("Conexao_BD.php");   
    
    switch ($_SESSION['pagina']){
        //Campo de login-------------------------------------------------------------------------
        case 'login':

            //Verifica se primeiro campo está vazio
            if(empty($_POST['Itsemail'])){
                $_SESSION['erroLogin'] = "Por favor, digite seu e-mail.";
                header("Location: login.php");
                die();
            }
            //Verifica se segundo campo está vazio
            if(empty($_POST['senha'])){
                $_SESSION['erroLogin'] = "Por favor, digite sua senha.";
                header("Location: login.php");
                die();
            }

            $email = mysqli_real_escape_string($conn, $_POST['Itsemail']); //Escapar de caracteres especiais, como aspas, prevenindo SQL injection
            $senha = mysqli_real_escape_string($conn, hash("sha3-224", $_POST['senha'])); 

            $queryPesquisa = "SELECT * FROM usuario WHERE email='$email'";

            if(empty(mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa)))){
                $_SESSION['erroLogin'] = "E-mail inválido.";
                header("Location: login.php");
                die();
            }            
            else{
                $queryPesquisa = $queryPesquisa . " AND senha='$senha'";
                $resultUsuario = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
                if(empty($resultUsuario)){
                    $_SESSION['erroLogin'] = "Senha inválida.";
                    header("Location: login.php");
                    die();
                }
                else{
                    $_SESSION['usuario'] = $resultUsuario['email'];
                    header("Location: main.php");
                    die();
                }
            }
        break;

        //Campo de cadastro---------------------------------------------------------------------
        case 'cadastro':

            $nome = $_POST['Itsname'];
            $email = $_POST['Itsemail'];
            $senha = $_POST['Itspassword'];
            $cSenha = $_POST['Itspassword2'];
            $cep = preg_replace("/[^\d]+/", '', $_POST['Itscep']);
            $numero = $_POST['Itsnumero'];
            $tipoPessoa = $_POST['Itstipopessoa'];
            $cpfCnpj = preg_replace("/[^\d]+/", '', $_POST['Itscpfcnpj']);
            $fotoPerfil = file_get_contents("style/bin/user1.bin");

            include_once("verificaCampos.php");
        
            validaCamposCadastro($nome,$email,$senha,$cSenha,$cep,$numero,$tipoPessoa,$cpfCnpj);

            $senha = mysqli_real_escape_string($conn, hash("sha3-224", $senha));

            //VERIFICA SE O EMAIL JÁ FOI CADASTRADO
            $queryPesquisa = "SELECT * FROM usuario WHERE email = '$email' LIMIT 1";
            //Encontrado um usuario na tabela usuário com os mesmos dados digitado no formulário (EMAIL)
            if(!empty(mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa)))){
                $_SESSION['erroCadastro'] = "E-mail já cadastrado.";
                header("Location: cadastro.php");
                die();
            }

            //VERIFICA SE O CPF/CNPJ JÁ FOI CADASTRADO
            $queryPesquisa = "SELECT * FROM usuario WHERE cpfcnpj = '$cpfCnpj' LIMIT 1";
            //Encontrado um usuario na tabela usuário com os mesmos dados digitado no formulário (EMAIL)
            if(!empty(mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa)))){
                if($tipoPessoa == "F"){
                    $_SESSION['erroCadastro'] = "CPF já cadastrado.";
                }
                else{
                    $_SESSION['erroCadastro'] = "CNPJ já cadastrado.";
                }
                header("Location: cadastro.php");
                die();
            }

            $queryInserir = "INSERT INTO usuario (nome, email, senha, cep, numero, tipoPessoa, cpfcnpj, fotoPerfil) 
                             VALUES ('$nome', '$email', '$senha', '$cep', '$numero', '$tipoPessoa', '$cpfCnpj', '$fotoPerfil')";
                  
            if(!empty(mysqli_query($conn, $queryInserir)))
            {
                header("Location: login.php");
                die();
                //Usuário cadastrado com sucesso
            }
            else{
                $_SESSION['erroCadastro'] = "Erro inesperado ao tentar cadastrar usuário. Código de erro: " . mysqli_errno($conn);
                header("Location: cadastro.php");
                die();
            }
        break;
        
        //Campo de Logout---------------------------------------------------------------------
        case 'perfil':
            $_SESSION['usuario'] = null;
            header("Location: home.php");
            die();
        break;

        case 'main':
            $_SESSION['usuario'] = null;
            header("Location: home.php");
            die();
        break;
        
        //Campo de alteração------------------------------------------------------------------
        case 'config':

            //Recebe dados do usuário-------------------------------------------------
                $usuario = $_SESSION['usuario'];
                $queryPesquisa = "SELECT * FROM usuario WHERE email = '$usuario' LIMIT 1";
                $resultUsuario = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
                if(empty($resultUsuario)){
                    $_SESSION['usuario'] = null;
                    $_SESSION['erroHome'] = "Não foi possível recuperar os dados do usuário.<br> Código de erro: " . mysqli_errno($conn);
                    header("Location: home.php");
                }

            //Deleta conta------------------------------------------------------------
                if($_POST['apagaConta'] == "s"){
                    $queryDeletar = "DELETE FROM eventostransporte WHERE cpfcnpj=" . $resultUsuario['cpfcnpj'];
                    mysqli_query($conn,$queryDeletar);
                    $queryDeletar = "DELETE FROM usuario WHERE email='$usuario'";
                    if(mysqli_query($conn,$queryDeletar)){
                        $_SESSION['usuario'] = null;
                        header("Location: home.php");
                        die();
                    }
                    else{
                        echo mysqli_error($conn);
                    }
                }

            //Recebe valores da config e verifica se o usuário alterou ou não----------

                //PERSONALIZAÇÃO*/
                $arquivo = '';
                $fotoTamanho = '';
                $fotoRecebida = '';
                //Verifica se o usuário escolheu alguma foto da própria máquina
                if(!empty($_FILES['Itsfoto']['tmp_name'])){
                    $fotoRecebida = "usuario";
                    //Nome do arquivo
                    $fotoNome = $_FILES['Itsfoto']['name'];
                    //Tamanho do arquivo em Bytes
                    $fotoTamanho = $_FILES['Itsfoto']['size'];
                    //Extensão do arquivo
                    $fotoTipo = $_FILES['Itsfoto']['type'];
                    //Nome pelo qual o arquivo é reconhecido pelo código
                    $arquivo = $_FILES['Itsfoto']['tmp_name'];
                }
                else{
                    if(!empty($_POST['Itsperfil'])){
                        $fotoRecebida = "preset";
                        //Recebe o valor da foto do site selecionada
                        $fotoNome = $_POST['Itsperfil'];
                        //Define que a foto é do site
                        $fotoTipo = "site";
                    }
                    else{
                        $fotoNome = $resultUsuario['fotoNome'];
                        $fotoTipo = $resultUsuario['fotoTipo'];
                        $fotoTamanho = "";
                        $fotoPerfil = addslashes($resultUsuario['fotoPerfil']);
                    }
                }

                //Recebe valor do plano de fundo
                $fotoBg = $_POST['Itsbg'];

                //Recebe valor do plano de fundo
                $fotoBg = $_POST['Itsbg'];

                //DADOS PESSOAIS
                $email = $_POST['Itsemail'];
                $nome = $_POST['Itsname'];
                $senha = $_POST['Itspassword'];
                $cSenha = $_POST['Itspassword2'];
                $cep = preg_replace("/[^\d]+/", '', $_POST['Itscep']);
                $numero = $_POST['Itsnumero'];
                $cnpj = preg_replace("/[^\d]+/", '', $_POST['Itscnpj']);
                $descricao = $_POST['Itsdescricao'];

                //MODO TRANSPORTADOR
                if(isset($_POST['Itstransportador'])){
                    $transportador = "S";
                }
                else{
                    $transportador = "N";
                }
                $modeloVeiculo = $_POST['Itsmodelo'];
                $placaVeiculo =  strtoupper($_POST['Itsplaca']);
                $corVeiculo = $_POST['Itscor'];

            //Valida dados---------------------------------------------------------------

                //Verifica se o e-mail já está registrado no site
                $queryPesquisa = "SELECT * FROM usuario WHERE email = '$email' LIMIT 1";
                if(!empty(mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa))) && $usuario != $email){
                    $_SESSION['erroConfig'] = "E-mail já cadastrado.";
                    header("Location: config.php");
                    die();
                }

                //Verifica se o cnpj já está registrado no site
                if($resultUsuario['tipoPessoa'] == "J"){
                    $queryPesquisa = "SELECT * FROM usuario WHERE cpfcnpj = '$cnpj' LIMIT 1";
                    if(!empty(mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa))) && $resultUsuario['cpfcnpj'] != $cnpj){
                        $_SESSION['erroConfig'] = "E-mail já cadastrado.";
                        header("Location: config.php");
                        die();
                    }
                }

                include_once("verificaCampos.php");
                validaCamposConfig($nome,$email,$senha,$cSenha,$resultUsuario['tipoPessoa'],$cnpj,$cep,$numero,$arquivo,$fotoNome,$fotoTipo,$fotoTamanho,$fotoRecebida,$transportador,$modeloVeiculo,$placaVeiculo);

            //Prepara a query verificando o que o usuário de fato vai alterar--------------------------------
                $queryAtualizar = "UPDATE usuario SET ";

                //Verifica o tipo de foto
                switch($fotoRecebida){
                    case "usuario":
                        //Abre o arquivo em formato rb (Read, com o parametro b para arquivos não textuais)
                        $fotoAberta = fopen($_FILES['Itsfoto']['tmp_name'],"rb");

                        //Extrai o conteúdo do arquivo (fread(arquivoAberto,atéQueByteDeveExtrair))
                        //Já que devemos extrair toda a imagem, a largura da extração é o tamanho máximo do arquivo
                        //addslashes = Função que adiciona \ antes de caracteres especiais para sua leitura
                        $fotoPerfil = addslashes(fread($fotoAberta,$fotoTamanho));

                        //Fecha arquivo
                        fclose($fotoAberta);
                    break;
                    case "preset":
                        //Extrai o conteúdo binário do arquivo do site, que será traduzido de 1 a 6 de acordo com a seleção
                        $fotoPerfil = file_get_contents("style/bin/user" . $fotoNome . ".bin");
                    break;
                }

                //Verifica se a foto mudou o tipo (usuário/site)
                if($fotoTipo != $resultUsuario['fotoTipo']){
                    $queryAtualizar = $queryAtualizar  . "fotoTipo='$fotoTipo',";
                }

                //Verifica se a foto mudou o nome 
                if($fotoNome != $resultUsuario['fotoNome']){
                    $queryAtualizar = $queryAtualizar  . "fotoNome='$fotoNome',";
                }
        
                //Verifica se é outro arquivo no lugar
                if($fotoPerfil != addslashes($resultUsuario['fotoPerfil'])){
                    $queryAtualizar  = $queryAtualizar  . "fotoPerfil='$fotoPerfil',";
                }

                //Verifica se a pessoa vai alterar o plano de fundo do perfil 
                if($fotoBg != $resultUsuario['fotoBg']){
                    $queryAtualizar  = $queryAtualizar  . "fotoBg='$fotoBg',";
                }

                //Verifica se a pessoa vai alterar o nome 
                if($nome != $resultUsuario['nome']){
                    $queryAtualizar  = $queryAtualizar  . "nome='$nome',";
                }

                //Verifica se a pessoa vai alterar o e-mail
                if($email != $resultUsuario['email']){
                    $queryAtualizar  = $queryAtualizar  . "email='$email',";
                }

                //Verifica se a pessoa vai alterar a senha
                if(!empty($_POST['Itspassword'])){
                    //Criptografa a senha
                    $senha = mysqli_real_escape_string($conn, hash("sha3-224", $senha));
                    if($senha != $resultUsuario['senha']){
                        $queryAtualizar  = $queryAtualizar  . "senha='$senha',";
                    }
                }

                //Verifica se a pessoa vai alterar o CEP
                if($cep != $resultUsuario['cep']){
                    $queryAtualizar = $queryAtualizar  . "cep='$cep',";
                }

                //Verifica se a pessoa vai alterar o CNPJ
                if($resultUsuario['tipoPessoa'] == "J"){
                    if($cnpj != $resultUsuario['cpfcnpj']){
                        $queryAtualizar  = $queryAtualizar  . "cpfcnpj='$cnpj',";
                    }
                }

                //Verifica se a pessoa vai alterar o número
                if($numero != $resultUsuario['numero']){
                    $queryAtualizar = $queryAtualizar  . "numero='$numero',";
                }

                //Verifica se a pessoa vai habilitar/desabilitar o modo transportador
                if($transportador != $resultUsuario['transportador']){
                    $queryAtualizar  = $queryAtualizar  . "transportador='$transportador',";
                }

                //Verifica se a pessoa é transportadora
                if($transportador == "S"){

                    //Verifica se a pessoa vai alterar o modelo do veiculo
                    if($modeloVeiculo != $resultUsuario['modeloCarro']){
                        $queryAtualizar = $queryAtualizar . "modeloCarro='$modeloVeiculo',";
                    }

                    //Verifica se a pessoa vai alterar a placa do veiculo
                    if($placaVeiculo != $resultUsuario['placaCarro']){
                        $queryAtualizar = $queryAtualizar . "placaCarro='$placaVeiculo',";
                    }

                    //Verifica se a pessoa vai alterar a cor do veiculo
                    if($corVeiculo != $resultUsuario['corCarro']){
                        $queryAtualizar = $queryAtualizar . "corCarro='$corVeiculo',";
                    }
                }
                else{
                    //Se a pessoa não for transportadora anula os campos relacionados
                    $queryAtualizar  = $queryAtualizar  . "modeloCarro='', placaCarro='', corCarro='#000000',";
                }

                //Retira a vírgula final e completa a query
                $queryAtualizar = substr_replace($queryAtualizar,"",-1);
                $queryAtualizar = $queryAtualizar . " WHERE email='$usuario'";

                if($queryAtualizar != "UPDATE usuario SET WHERE email='$usuario'"){
                    if(mysqli_query($conn,$queryAtualizar)){
                        //Verifica se a pessoa alterou o e-mail
                        if($email != $resultUsuario['email']){
                            $_SESSION['usuario'] = $email;
                        }
                        header("Location: perfil.php");
                        die();
                    }
                    else{
                        //DEU ERRO MERMÃO
                        $_SESSION['erroConfig'] = "Erro ao tentar atualizar seus dados, tente novamente.<br> Código de erro: " . mysqli_errno($conn);
                        echo mysqli_error($conn);
                        echo "<br>" . $queryAtualizar . "<br>" . $_POST['Itsperfil'];
                        die();
                        header("Location: config.php");
                        die();
                    }
                }
                else{
                    header("Location: perfil.php");
                    die();
                }
        break;

        //Campo de criação de item------------------------------------------------------------------
        case 'adicionarItem':
            //Recebe dados do usuário-------------------------------------------------
            $usuario = $_SESSION['usuario'];
            $queryPesquisa = "SELECT * FROM usuario WHERE email = '$usuario' LIMIT 1";
            $resultUsuario = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
            if(empty($resultUsuario)){
                $_SESSION['usuario'] = null;
                $_SESSION['erroHome'] = "Não foi possível recuperar os dados do usuário.<br> 
                                         Código de erro: " . mysqli_errno($conn);
                header("Location: home.php");
            }


            $NOME = $_POST['nome'];
            $CATEGORIA = $_POST['categoria'];
            $INFO = $_POST['info'];
            $TIPOPEDIDO = $_POST['radio_pedido'];
            if($TIPOPEDIDO == "option1"){
                $TIPOPEDIDO = "troca";
            }
            else{
                $TIPOPEDIDO = "doacao";
            }
            
            if(!empty($_FILES['arquivoImagem']['tmp_name'])){
                //Tamanho do arquivo
                $FOTOTAMANHO = $_FILES['arquivoImagem']['size'];
                //Extensão do arquivo
                $FOTOTIPO = $_FILES['arquivoImagem']['type'];
                //Nome pelo qual o arquivo é reconhecido pelo código
                $ARQUIVO = $_FILES['arquivoImagem']['tmp_name'];
            }
            else{
                $_SESSION['erroAdicionarItem'] = "Por favor, selecione uma imagem para seu item";
                header("Location: adicionarItem.php");
                die();  
            }

            include_once("verificaCampos.php");
            validaCamposPedido($NOME,$CATEGORIA,$INFO,$ARQUIVO,$FOTOTIPO,$FOTOTAMANHO);

            //Abre o arquivo de imagem em formato rb (Read, com o parametro b para arquivos não textuais)
            $FOTOABERTA = fopen($ARQUIVO,"rb");

            //Extrai o conteúdo do arquivo (fread(arquivoAberto,atéQueByteDeveExtrair))
            //Já que devemos extrair toda a imagem, a largura da extração é o tamanho máximo do arquivo
            //addslashes = Função que adiciona \ antes de caracteres especiais para sua leitura
            $FOTOPEDIDO = addslashes(fread($FOTOABERTA,$FOTOTAMANHO));

            //Fecha arquivo
            fclose($FOTOABERTA);

            $queryInserir = "INSERT INTO item (cpfcnpj, nome, categoria, info, intuito, imagem, imagemTipo) VALUES ('" . $resultUsuario['cpfcnpj'] . "', '$NOME', '$CATEGORIA', '$INFO', '$TIPOPEDIDO', '$FOTOPEDIDO','$FOTOTIPO')";

            if (mysqli_query($conn,$queryInserir)) {
                // pedido cadastrado com sucesso
                header("Location: estoque.php");
                die();                        
            }
            else{
                // pedido não cadastrado
                $_SESSION['erroAdicionarItem'] = "Ocorreu algum problema durante o armazenamento.<br>
                                                  Código de erro: " . mysqli_errno($conn);
                header("Location: adicionarItem.php");
                die();  
            }
        break;

        case "transacao":

            //Recebe dados do usuário-------------------------------------------------
            $usuario = $_SESSION['usuario'];
            $queryPesquisa = "SELECT * FROM usuario WHERE email='$usuario' LIMIT 1";
            $resultUsuario = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
            if(empty($resultUsuario)){
                $_SESSION['usuario'] = null;
                $_SESSION['erroHome'] = "Não foi possível recuperar os dados do usuário.<br> Código de erro: " . mysqli_errno($conn);
                header("Location: home.php");
                die();
            }

            $estagioTransacao = $_POST['Itsestagio'];
            $tipoTransacao = $_POST['Itstipo'];
            $idTransacao = $_POST['Itsitemprimario'];

            if($tipoTransacao == "troca"){
                $itemSecundario = $_POST['Itsitemsecundario'];
            }

            //Recebe dados da transação e define os tipos de pessoa
            $queryPesquisa = "SELECT * FROM transacao WHERE itemPrimario = $idTransacao LIMIT 1";
            $resultTransacao = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
            if(!empty($resultTransacao)){
                if($resultUsuario['cpfcnpj'] == $resultTransacao['cpfcnpj1']){
                    $cpfcnpjUsuario = $resultTransacao['cpfcnpj1'];
                    $confirmUsuario = "confirm1";
                    $cpfcnpjOutroUsuario = $resultTransacao['cpfcnpj2'];
                    $confirmOutroUsuario = "confirm2";
                }
                else{
                    $cpfcnpjUsuario = $resultTransacao['cpfcnpj2'];
                    $confirmUsuario = "confirm2";
                    $cpfcnpjOutroUsuario = $resultTransacao['cpfcnpj1'];
                    $confirmOutroUsuario = "confirm1";
                }
            }


            switch($estagioTransacao){
                case "Contatar":

                    //Cria a transação ou atualiza o estágio
                    if(empty($resultTransacao)){
                        $cpfcnpjUsuario = $resultUsuario['cpfcnpj'];
                        $queryPesquisa = "SELECT cpfcnpj FROM item WHERE id = $idTransacao LIMIT 1";
                        $resultItem = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
                        $cpfcnpjOutroUsuario = $resultItem['cpfcnpj'];
                        if($tipoTransacao == "troca"){
                            //Atualiza o item secundário para "em uso"
                            $queryUpdate = "UPDATE item SET emUso='s' WHERE id=" . $itemSecundario;
                            mysqli_query($conn,$queryUpdate);
                            $queryInserir = "INSERT INTO transacao (itemPrimario, estagio, cpfcnpj1, cpfcnpj2, itemSecundario) VALUES (" . $idTransacao . ", '" . $estagioTransacao . "', '" . $cpfcnpjOutroUsuario . "', '" . $cpfcnpjUsuario . "', $itemSecundario)";
                        }
                        else{
                            $queryInserir = "INSERT INTO transacao (itemPrimario, estagio, cpfcnpj1, cpfcnpj2) VALUES (" . $idTransacao . ", '" . $estagioTransacao . "', '" . $cpfcnpjOutroUsuario . "', '" . $cpfcnpjUsuario . "')";
                        }
                        mysqli_query($conn,$queryInserir);
                        //Atualiza o item primário para "em uso"
                        $queryUpdate = "UPDATE item SET emUso='s' WHERE id=" . $idTransacao;
                        mysqli_query($conn,$queryUpdate);

                        //Notifica o outro usuário
                        $tipoNotificacao = "nova";
                    }
                    else{
                        //Verifica se querem recusar a transação
                        if($_POST['Itscancelar'] == "s"){

                            //Atualiza os itens no processo
                            $queryUpdate = "UPDATE item SET emUso='n' WHERE id=" . $idTransacao;
                            mysqli_query($conn,$queryUpdate);
                            if($tipoTransacao == "troca"){
                                $queryUpdate = "UPDATE item SET emUso='n' WHERE id=" . $itemSecundario;
                                mysqli_query($conn,$queryUpdate);
                            }

                            //Deleta a transação
                            $queryDelete = "DELETE FROM transacao WHERE itemPrimario=" . $idTransacao;
                            mysqli_query($conn,$queryDelete);

                            //Deleta o chat
                            $queryDelete = "DELETE FROM chat WHERE id=" . $idTransacao;
                            mysqli_query($conn,$queryDelete);

                            //Notifica o outro usuário
                            $tipoNotificacao = "recusado";
                        }
                        else{
                            //Confirma o usuário
                            $queryUpdate = "UPDATE transacao SET $confirmUsuario='s' WHERE itemPrimario=" . $idTransacao;
                            mysqli_query($conn,$queryUpdate);

                            //Atualiza os dados da transação
                            $queryPesquisa = "SELECT * FROM transacao WHERE itemPrimario = $idTransacao LIMIT 1";
                            $resultTransacao = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
                            if(empty($resultTransacao)){
                                $_SESSION['erroMain'] = "Não foi possível recuperar os dados do outro usuário.<br> Código de erro: " . mysqli_errno($conn);
                                header("Location: main.php");
                                die();
                            }

                            //Verifica se foram para o outro estágio da transação
                            if($resultTransacao['confirm1'] == 's' && $resultTransacao['confirm2'] == 's'){
                                $queryUpdate = "UPDATE transacao SET $confirmUsuario='n', $confirmOutroUsuario='n', estagio='Confirmar' WHERE itemPrimario=" . $idTransacao;
                                mysqli_query($conn,$queryUpdate);
                                $tipoNotificacao = "estagio";
                            }
                        }
                    }
                break;

                case "Confirmar":

                    //Cancela a transação
                    if($_POST['Itscancelar'] == "s"){

                        //Deleta a transação
                        $queryDelete = "DELETE FROM transacao WHERE itemPrimario=" . $idTransacao;
                        mysqli_query($conn,$queryDelete);

                        //Atualiza os itens no processo
                        $queryUpdate = "UPDATE item SET emUso='n' WHERE id=" . $idTransacao;
                        mysqli_query($conn,$queryUpdate);
                        if($tipoTransacao == "troca"){
                            $queryUpdate = "UPDATE item SET emUso='n' WHERE id=" . $itemSecundario;
                            mysqli_query($conn,$queryUpdate);
                        }

                        //Deleta o chat
                        $queryDelete = "DELETE FROM chat WHERE id=" . $idTransacao;
                        mysqli_query($conn,$queryDelete);

                        //Notifica o outro usuário
                        $tipoNotificacao = "cancelamento";

                        //Notifica o transportador
                        if($resultTransacao['transportador'] != null){
                            $queryDelete = "DELETE FROM notificacao WHERE id=" . $_POST['transacao'] . " AND cpfcnpj='" . $resultTransacao['transportador'] . "'";
                            mysqli_query($conn,$queryUpdate);
                            $queryInserir = "INSERT INTO notificacao (id,cpfcnpj,tipo,remetente) VALUES (" . $_POST['transacao'] . ",'" . $resultTransacao['transportador'] . "', 'cancelaTransporte', '" . $resultTransacao['cpfcnpj1'] . "')";
                            mysqli_query($conn,$queryInserir);
                        }
                    }
                    else{
                        //Atualiza confirmação de estágio do usuário
                        $queryUpdate = "UPDATE transacao SET $confirmUsuario='s' WHERE itemPrimario=" . $idTransacao;
                        mysqli_query($conn,$queryUpdate);

                        //Atualiza os dados da transação-------------------------------------------------
                        $queryPesquisa = "SELECT * FROM transacao WHERE itemPrimario = $idTransacao LIMIT 1";
                        $resultTransacao = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
                        if(empty($resultTransacao)){
                            $_SESSION['erroMain'] = "Não foi possível recuperar os dados do outro usuário.<br> Código de erro: " . mysqli_errno($conn);
                            header("Location: main.php");
                            die();
                        }


                        //Verifica se foram para o outro estágio da transação
                        if($resultTransacao['confirm1'] == 's' && $resultTransacao['confirm2'] == 's'){
                            $queryUpdate = "UPDATE transacao SET $confirmUsuario='n', $confirmOutroUsuario='n', estagio='Concluir' WHERE itemPrimario=" . $idTransacao;
                            mysqli_query($conn,$queryUpdate);
                        }
                        $tipoNotificacao = "estagio";
                    }
                break;

                case "Concluir":
                    //Atualiza confirmação de estágio do usuário
                    $queryUpdate = "UPDATE transacao SET $confirmUsuario='s' WHERE itemPrimario=" . $idTransacao;
                    mysqli_query($conn,$queryUpdate);

                    //Recebe dados da transação
                    $queryPesquisa = "SELECT * FROM transacao WHERE itemPrimario = $idTransacao LIMIT 1";
                    $resultTransacao = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
                    if(empty($resultTransacao)){
                        $_SESSION['erroMain'] = "Não foi possível recuperar os dados do outro usuário.<br> Código de erro: " . mysqli_errno($conn);
                        header("Location: main.php");
                        die();
                    }

                    //Verifica se concluíram a transação
                    if($resultTransacao['confirm1'] == 's' && $resultTransacao['confirm2'] == 's'){
                        //Gasto e ganho de ticket caso seja doação
                        if($tipoTransacao == "doacao"){

                            $resultPessoa = mysqli_fetch_assoc(mysqli_query($conn,"SELECT tickets FROM usuario WHERE cpfcnpj='" . $resultTransacao['cpfcnpj2'] . "'"));
                            $queryUpdate = "UPDATE usuario SET tickets=" . (intval($resultPessoa['tickets'],10)-1) . " WHERE cpfcnpj='" . $resultTransacao['cpfcnpj2'] . "'";
                            mysqli_query($conn,$queryUpdate);

                            $resultPessoa = mysqli_fetch_assoc(mysqli_query($conn,"SELECT tickets FROM usuario WHERE cpfcnpj='" . $resultTransacao['cpfcnpj1'] . "'"));
                            $queryUpdate = "UPDATE usuario SET tickets=" . (intval($resultUsuario['tickets'],10)+1) . " WHERE cpfcnpj='" . $resultTransacao['cpfcnpj1'] . "'";
                            mysqli_query($conn,$queryUpdate);
                        }

                        //Deleta a transação
                        $queryDelete = "DELETE FROM transacao WHERE itemPrimario=" . $idTransacao;
                        mysqli_query($conn,$queryDelete);

                        //Deleta os itens
                        $queryUpdate = "DELETE FROM item WHERE id=" . $idTransacao;
                        if($tipoTransacao == "troca"){
                            $queryUpdate = $queryUpdate + "OR id=" . $itemSecundario;
                        }
                        mysqli_query($conn,$queryUpdate);

                        //Deleta o chat
                        $queryDelete = "DELETE FROM chat WHERE id=" . $idTransacao;
                        mysqli_query($conn,$queryDelete);

                        //Notifica o outro usuário
                        $tipoNotificacao = "conclusao";

                        //Notifica o transportador
                        if($resultTransacao['transportador'] != null){
                            $queryDelete = "DELETE FROM notificacao WHERE id=" . $_POST['transacao'] . " AND cpfcnpj='" . $resultTransacao['transportador'] . "'";
                            mysqli_query($conn,$queryUpdate);
                            $queryInserir = "INSERT INTO notificacao (id,cpfcnpj,tipo,remetente) VALUES ('" . $_POST['transacao'] . "','" . $resultTransacao['transportador'] . "', 'conclusao', '" . $resultTransacao['cpfcnpj1'] . "')";
                            mysqli_query($conn,$queryInserir);
                        }
                    }
                    else{
                        //Notifica o outro usuário
                        $tipoNotificacao = "estagio";
                    }
                break;
            }
            $queryDelete = "DELETE FROM notificacao WHERE id=$idTransacao AND tipo!='chat' AND cpfcnpj='$cpfcnpjOutroUsuario' OR cpfcnpj='$cpfcnpjUsuario'";
            mysqli_query($conn,$queryDelete);
            $queryInserir = "INSERT INTO notificacao (id, cpfcnpj, tipo, remetente) VALUES ($idTransacao, '$cpfcnpjOutroUsuario', '$tipoNotificacao', '$cpfcnpjUsuario')";
            mysqli_query($conn,$queryInserir);
            $_SESSION['idTransacao'] = $idTransacao;
            if($tipoNotificacao != "estagio" && $tipoNotificacao != "nova" && $tipoNotificacao != "transporte"){
                header("Location: perfil.php");
            }
            else{
                header("Location: transacao.php");
            }
        break;
    }
?>