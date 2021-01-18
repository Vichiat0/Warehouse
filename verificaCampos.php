<?php
    function validaCamposCadastro($nome,$email,$senha,$cSenha,$cep,$numero,$tipoPessoa,$cpfCnpj){

        //VALIDA NOME----------------------------------------------------------------------------------------------------------

        //Verifica se nome ta vazio ou com espaço em branco
        if(strlen($nome) == 0 || trim($nome) == ""){
            $_SESSION['erroCadastro'] = "Por favor, digite o seu nome.";
            header("Location: cadastro.php");
            die();
        }

        //Verifica se o nome tem menos de 3 caracteres
        if(strlen($nome) < 3){
            $_SESSION['erroCadastro'] = "Por favor, digite o seu nome inteiro.";
            header("Location: cadastro.php");
            die();
        }

        //VALIDA EMAIL---------------------------------------------------------------------------------------------------------

        //Verifica se o e-mail possui ".com" ou "@" ou se ele está com espaço em branco
        if(strpos($email,".com") === false || strpos($email,"@") === false || trim($email) == "") {
            $_SESSION['erroCadastro'] = "Por favor, digite um e-mail válido.";
            header("Location: cadastro.php");
            die();
        }

        //VALIDA SENHA---------------------------------------------------------------------------------------------------------

        //Verifica se a senha está com um espaço em branco
        if (trim($senha) == ""){
            $_SESSION['erroCadastro'] = "Por favor, digite sua senha.";
            header("Location: cadastro.php");
            die();
        }

        //Verifica se a senha e a confimação são iguais
        if($senha != $cSenha){
            $_SESSION['erroCadastro'] = "A senha e sua confirmação devem ser idênticas";
            header("Location: cadastro.php");
            die();
        }

        //VALIDA CPF/CNPJ--------------------------------------------------------------------------------------------------------

        //Variáveis para cálculo dos DVs do CPF/CNPJ
        $Soma = 0;
        $Resto = 0;

        //Verifica se é CPF ou CNPJ
        if ($tipoPessoa == "F"){
            //Verifica se o campo está com espaço em branco
            if (trim($cpfCnpj) == ""){
                $_SESSION['erroCadastro'] = "Por favor, digite seu CPF.";
                header("Location: cadastro.php");
                die();
            }

            //Verifica se o CPF tem menos de 11 caracteres ou segue o padrão "0000000000" ou "11111111111" e assim por diante
            if(strlen($cpfCnpj) != 11 ||
                preg_match_all("/0/",$cpfCnpj) == 11 ||
                preg_match_all("/1/",$cpfCnpj) == 11 ||
                preg_match_all("/2/",$cpfCnpj) == 11 ||
                preg_match_all("/3/",$cpfCnpj) == 11 ||
                preg_match_all("/4/",$cpfCnpj) == 11 ||
                preg_match_all("/5/",$cpfCnpj) == 11 ||
                preg_match_all("/6/",$cpfCnpj) == 11 ||
                preg_match_all("/7/",$cpfCnpj) == 11 ||
                preg_match_all("/8/",$cpfCnpj) == 11 ||
                preg_match_all("/9/",$cpfCnpj) == 11){
                $_SESSION['erroCadastro'] = "Por favor, digite um CPF válido.";
                header("Location: cadastro.php");
                die();
            }
            
            //Cálculo dos DVs do CPF
            
            //DV1
            //Dígitos - D1 D2 D3 D4 D5 D6 D7 D8 D9  \
            //          *  *  *  *  *  *  *  *  *    Soma o resultado da múltiplicação de todos
            //Pesos   - 10 9  8  7  6  5  4  3  2  /

            //Resto = Resultado % 11 
            //DV1 = 11 - Resto
            //Se o DV1 for >= 10, passa a ser 0
            for ($i=1; $i<=9; $i++){ 
                $Soma = $Soma + substr($cpfCnpj,$i-1, 1) * (11 - $i);
            }

            $Resto = $Soma % 11 < 2 ? 0 : 11 - ($Soma % 11);

            if ($Resto != substr($cpfCnpj,9, 1)){
                $_SESSION['erroCadastro'] = "Por favor, digite um CPF válido.";
                header("Location: cadastro.php");
                die();
            } 

            $Soma = 0;

            //DV2
            //Dígitos   - D1 D2 D3 D4 D5 D6 D7 D8 D9  \
            //            *  *  *  *  *  *  *  *  *    Soma o resultado da múltiplicação de todos
            //Pesos(+1) - 11 10 9  8  7  6  5  4  3  /

            //Resto = Resultado % 11 
            //DV1 = 11 - Resto
            //Se o DV1 for >= 10, passa a ser 0
            for ($i = 1; $i <= 10; $i++){
                $Soma = $Soma + substr($cpfCnpj,$i-1, 1) * (12 - $i);
            }

            $Resto = $Soma % 11 < 2 ? 0 : 11 - ($Soma % 11);

            if ($Resto != substr($cpfCnpj,10, 1)){
                $_SESSION['erroCadastro'] = "Por favor, digite um CPF válido.";
                header("Location: cadastro.php");
                die();
            }

        }
        else{
        //CNPJ

            //Verifica se o campo está com espaço em branco
            if (trim($cpfCnpj) == ""){
                $_SESSION['erroCadastro'] = "Por favor, digite seu CNPJ.";
                header("Location: cadastro.php");
                die();
            }

            //Verifica se o CNPJ possui 14 dígitos ou segue o padrão "0000000000000" ou "11111111111111" e assim por diante
            if (strlen($cpfCnpj) != 14 || 
                preg_match_all("/0/",$cpfCnpj) == 14 ||
                preg_match_all("/1/",$cpfCnpj) == 14 ||
                preg_match_all("/2/",$cpfCnpj) == 14 ||
                preg_match_all("/3/",$cpfCnpj) == 14 ||
                preg_match_all("/4/",$cpfCnpj) == 14 ||
                preg_match_all("/5/",$cpfCnpj) == 14 ||
                preg_match_all("/6/",$cpfCnpj) == 14 ||
                preg_match_all("/7/",$cpfCnpj) == 14 ||
                preg_match_all("/8/",$cpfCnpj) == 14 ||
                preg_match_all("/9/",$cpfCnpj) == 14){
                $_SESSION['erroCadastro'] = "Por favor, digite um CNPJ válido.";
                header("Location: cadastro.php");
                die();
            }
     
            //Cálculo dos DVs do CNPJ
            
            //DV1
            //Dígitos - D1 D2 D3 D4 D5 D6 D7 D8 D9 D10 D11 D12  \
            //          *  *  *  *  *  *  *  *  *   *   *   *    Soma o resultado da múltiplicação de todos
            //Pesos   - 5  4  3  2  9  8  7  6  5   4   3   2  /


            //Resto = Resultado % 11 
            //DV1 = 11 - Resto
            //Se o DV1 for >= 10, passa a ser 0
            $posBloco = 5;
            $Soma = 0;

            for ($i= 1; $i<=12; $i++){
                if((10 - $posBloco) < 2){
                    $posBloco = 1;
                }
                $Soma = $Soma + substr($cpfCnpj,$i-1, 1) * (10 - $posBloco);
                $posBloco++;
            }

            $Resto = $Soma % 11 < 2 ? 0 : 11 - ($Soma % 11);

            if ($Resto != substr($cpfCnpj,12,1)){
                $_SESSION['erroCadastro'] = "Por favor, digite um CNPJ válido.";
                header("Location: cadastro.php");
                die();
            }

            //DV2                                               /-> Dígito 13 a mais (sendo o DV calculado anteriormente)
            //Dígitos - D1 D2 D3 D4 D5 D6 D7 D8 D9 D10 D11 D12 D13  \
            //          *  *  *  *  *  *  *  *  *   *   *   *   *    Soma o resultado da múltiplicação de todos
            //Pesos   - 6  5  4  3  2  9  8  7  6   5   4   3   2  /
            //         /-> Peso(+1) nos primeiros 5 dígitos


            //Resto = Resultado % 11 
            //DV1 = 11 - Resto
            //Se o DV1 for >= 10, passa a ser 0
            $Soma = 0;
            $posBloco = 4;
     
            for ($i= 1; $i<=13; $i++){
                if((10 - $posBloco) < 2){
                    $posBloco = 1;
                }
                $Soma = $Soma + substr($cpfCnpj,$i-1, 1) * (10 - $posBloco);
                $posBloco++;
            }

            $Resto = $Soma % 11 < 2 ? 0 : 11 - ($Soma % 11);

            if ($Resto != substr($cpfCnpj,13,1)){
                $_SESSION['erroCadastro'] = "Por favor, digite um CNPJ válido.";
                header("Location: cadastro.php");
                die();
            }
       
        }


        //VALIDA CEP------------------------------------------------------------------------------------------------------------

        //Verifica se o CEP está com um espaço em branco
        if (trim($cep) == ""){
            $_SESSION['erroCadastro'] = "Por favor, digite seu CEP.";
            header("Location: cadastro.php");
            die();
        }

        //Verifica se o CEP tem 8 dígitos ou se existe
        if (strlen($cep) != 8){
            $_SESSION['erroCadastro'] = "Por favor, digite um CEP válido.";
            header("Location: cadastro.php");
            die();
        }
        else{
            $url = "http://viacep.com.br/ws/$cep/xml/";
            $xml = simplexml_load_file($url);
            if($xml->erro == true){
                $_SESSION['erroCadastro'] = "Por favor, digite um CEP válido.";
                header("Location: cadastro.php");
                die();
            }
        }

        //VALIDA NÚMERO----------------------------------------------------------------------------------------------------------

        //Verifica se o número está vazio
        if (trim($numero) == ""){
            $_SESSION['erroCadastro'] = "Por favor, digite o número do local.";
            header("Location: cadastro.php");
            die();
        }

        //Verifica se o número tem 2 letras 123a(1 letra) 123as(2 letras)
        if (preg_match_all("/[^0-9]/",$numero) > 1){
            $_SESSION['erroCadastro'] = "Por favor, digite um número válido.";
            header("Location: cadastro.php");
            die();
        }
    }

    function validaCamposConfig($nome,$email,$senha,$cSenha,$tipoPessoa,$cnpj,$cep,$numero,$arquivo,$fotoNome,$fotoTipo,$fotoTamanho,$fotoRecebida,$transportador,$marcaModeloVeiculo,$placaVeiculo){

        //VALIDA FOTO----------------------------------------------------------------------------------------------------------
        
        switch($fotoRecebida){
            case "usuario":
                //Verifica se o arquivo foi enviado corretamente pelo método POST
                if(!is_uploaded_file($arquivo)){
                    $_SESSION['erroConfig'] = "O arquivo de imagem não foi enviado corretamente, tente novamente.";
                    header("Location: config.php");
                    die();
                }

                //Verifica se a extensão do arquivo é suportada (PNG, JPG E JPEG)
                if($fotoTipo != "image/png" && $fotoTipo != "image/jpg" && $fotoTipo != "image/jpeg"){
                    $_SESSION['erroConfig'] = "Tipo de arquivo de imagem não suportado (Extensões suportadas: JPG, JPEG e PNG).";
                    header("Location: config.php");
                    die();
                }

                //Verifica se o arquivo está corrompido
                if(getimagesize($arquivo) == false){
                    $_SESSION['erroConfig'] = "Arquivo de imagem pode estar corrompido, tente selecionar outro.";
                    header("Location: config.php");
                    die();
                }

                //Verifica o tamanho do arquivo
                if($fotoTamanho > "16777216"){
                    $_SESSION['erroConfig'] = "Arquivo de imagem grande demais (Tamanho suportado: 16MB).";
                    header("Location: config.php");
                    die();
                }
            break;

            case "preset":
                //Verifica se houve algum erro ao extrair o número da foto selecionada
                if($fotoNome < 1 || $fotoNome > 6){
                    $_SESSION['erroConfig'] = "Erro ao selecionar imagem do site, tente novamente.";
                    header("Location: config.php");
                    die();
                }
            break;
        }
        
        //VALIDA NOME----------------------------------------------------------------------------------------------------------

        //Verifica se nome ta vazio ou com espaço em branco
        if(strlen($nome) == 0 || trim($nome) == ""){
            $_SESSION['erroConfig'] = "Por favor, digite o seu nome." . $nome;
            header("Location: config.php");
            die();
        }

        //Verifica se o nome tem menos de 3 caracteres
        if(strlen($nome) < 3){
            $_SESSION['erroConfig'] = "Por favor, digite o seu nome inteiro.";
            header("Location: config.php");
            die();
        }

        //VALIDA EMAIL---------------------------------------------------------------------------------------------------------

        //Verifica se o e-mail possui ".com" ou "@" ou se ele está com espaço em branco
        if(strpos($email,".com") === false || strpos($email,"@") === false || trim($email) == "") {
            $_SESSION['erroConfig'] = "Por favor, digite um e-mail válido.";
            header("Location: config.php");
            die();
        }

        //VALIDA SENHA---------------------------------------------------------------------------------------------------------

        //Verifica se a senha e a confimação são iguais
        if($senha != $cSenha){
            $_SESSION['erroConfig'] = "A senha e sua confirmação devem ser idênticas";
            header("Location: config.php");
            die();
        }

        //VALIDA CNPJ
        if($tipoPessoa == "J"){

            //Verifica se o CNPJ possui 14 dígitos ou segue o padrão "0000000000000" ou "11111111111111" e assim por diante
            if (strlen($cnpj) != 14 || 
                preg_match_all("/0/",$cnpj) == 14 ||
                preg_match_all("/1/",$cnpj) == 14 ||
                preg_match_all("/2/",$cnpj) == 14 ||
                preg_match_all("/3/",$cnpj) == 14 ||
                preg_match_all("/4/",$cnpj) == 14 ||
                preg_match_all("/5/",$cnpj) == 14 ||
                preg_match_all("/6/",$cnpj) == 14 ||
                preg_match_all("/7/",$cnpj) == 14 ||
                preg_match_all("/8/",$cnpj) == 14 ||
                preg_match_all("/9/",$cnpj) == 14){
                $_SESSION['erroConfig'] = "Por favor, digite um CNPJ válido.";
                header("Location: config.php");
                die();
            }
     
            //Cálculo dos DVs do CNPJ
            
            //DV1
            //Dígitos - D1 D2 D3 D4 D5 D6 D7 D8 D9 D10 D11 D12  \
            //          *  *  *  *  *  *  *  *  *   *   *   *    Soma o resultado da múltiplicação de todos
            //Pesos   - 5  4  3  2  9  8  7  6  5   4   3   2  /


            //Resto = Resultado % 11 
            //DV1 = 11 - Resto
            //Se o DV1 for >= 10, passa a ser 0
            $posBloco = 5;
            $Soma = 0;

            for ($i= 1; $i<=12; $i++){
                if((10 - $posBloco) < 2){
                    $posBloco = 1;
                }
                $Soma = $Soma + substr($cnpj,$i-1, 1) * (10 - $posBloco);
                $posBloco++;
            }

            $Resto = $Soma % 11 < 2 ? 0 : 11 - ($Soma % 11);

            if ($Resto != substr($cnpj,12,1)){
                $_SESSION['erroConfig'] = "Por favor, digite um CNPJ válido.";
                header("Location: config.php");
                die();
            }

            //DV2                                               /-> Dígito 13 a mais (sendo o DV calculado anteriormente)
            //Dígitos - D1 D2 D3 D4 D5 D6 D7 D8 D9 D10 D11 D12 D13  \
            //          *  *  *  *  *  *  *  *  *   *   *   *   *    Soma o resultado da múltiplicação de todos
            //Pesos   - 6  5  4  3  2  9  8  7  6   5   4   3   2  /
            //         /-> Peso(+1) nos primeiros 5 dígitos


            //Resto = Resultado % 11 
            //DV1 = 11 - Resto
            //Se o DV1 for >= 10, passa a ser 0
            $Soma = 0;
            $posBloco = 4;
     
            for ($i= 1; $i<=13; $i++){
                if((10 - $posBloco) < 2){
                    $posBloco = 1;
                }
                $Soma = $Soma + substr($cnpj,$i-1, 1) * (10 - $posBloco);
                $posBloco++;
            }

            $Resto = $Soma % 11 < 2 ? 0 : 11 - ($Soma % 11);

            if ($Resto != substr($cnpj,13,1)){
                $_SESSION['erroConfig'] = "Por favor, digite um CNPJ válido.";
                header("Location: config.php");
                die();
            }
        }

        //VALIDA CEP------------------------------------------------------------------------------------------------------------

        //Verifica se o CEP está com um espaço em branco
        if (trim($cep) == ""){
            $_SESSION['erroConfig'] = "Por favor, digite seu CEP.";
            header("Location: config.php");
            die();
        }

        //Verifica se o CEP tem 8 dígitos ou se existe
        if (strlen($cep) != 8){
            $_SESSION['erroConfig'] = "Por favor, digite um CEP válido.";
            header("Location: config.php");
            die();
        }
        else{
            $resultCep = simplexml_load_file("http://viacep.com.br/ws/$cep/xml/");
            if($resultCep->erro == true){
                $_SESSION['erroConfig'] = "Por favor, digite um CEP válido.";
                header("Location: config.php");
                die();
            }
        }

        //VALIDA NÚMERO----------------------------------------------------------------------------------------------------------

        //Verifica se o número está vazio
        if (trim($numero) == ""){
            $_SESSION['erroConfig'] = "Por favor, digite o número do local.";
            header("Location: config.php");
            die();
        }

        //Verifica se o número tem 2 letras 123a(1 letra) 123as(2 letras)
        if (preg_match_all("/[^0-9]/",$numero) > 1){
            $_SESSION['erroConfig'] = "Por favor, digite um número válido.";
            header("Location: config.php");
            die();
        }

        //VALIDA TRANSPORTE
        if($transportador == "S"){
            //Verifica se a pessoa digitou a marca e o modelo do veículo
            if(strlen($marcaModeloVeiculo) == 0 || trim($marcaModeloVeiculo) == ""){
                $_SESSION['erroConfig'] = "Por favor, digite a marca e o modelo de seu veículo.";
                header("Location: config.php");
                die();
            }

            //Verifica se a pessoa digitou a placa do veículo
            if(strlen($placaVeiculo) == 0 || trim($placaVeiculo) == ""){
                $_SESSION['erroConfig'] = "Por favor, digite a placa de seu veículo.";
                header("Location: config.php");
                die();
            }
        }
    }

    function validaCamposPedido($nome,$categoria,$info,$arquivo,$fotoTipo,$fotoTamanho){

        //VALIDA TÍTULO----------------------------------------------------------------------------------------------------------

        //Verifica se título ta vazio ou com espaço em branco
        if(strlen($nome) == 0 || trim($nome) == ""){
            $_SESSION['erroAdicionarItem'] = "Por favor, digite o nome do item.";
            header("Location: adicionarItem.php");
            die();
        }

        //VALIDA CATEGORIA----------------------------------------------------------------------------------------------------------

        //Verifica se a categoria ta vazia ou com espaço em branco
        if(strlen($categoria) == 0 || trim($categoria) == ""){
            $_SESSION['erroAdicionarItem'] = "Por favor, escolha a categoria do item.";
            header("Location: adicionarItem.php");
            die();
        }

        //VALIDA INFORMAÇÕES DO PEDIDO----------------------------------------------------------------------------------------------------------

        //Verifica se as informações tão vazias ou com espaço em branco
        if(strlen($info) == 0 || trim($info) == ""){
            $_SESSION['erroAdicionarItem'] = "Por favor, digite informações sobre o item.";
            header("Location: adicionarItem.php");
            die();
        }

        //VALIDA IMAGEM--------------------------------------------------------------------------------------------------------------
        //Verifica se o arquivo foi enviado corretamente pelo método POST
        if(!is_uploaded_file($arquivo)){
            $_SESSION['erroAdicionarItem'] = "O arquivo de imagem não foi enviado corretamente, tente novamente.";
            header("Location: adicionarItem.php");
            die();
        }

        //Verifica se a extensão do arquivo é suportada (PNG, JPG E JPEG)
        if($fotoTipo != "image/png" && $fotoTipo != "image/jpg" && $fotoTipo != "image/jpeg"){
            $_SESSION['erroAdicionarItem'] = "Tipo de arquivo de imagem não suportado (Extensões suportadas: JPG, JPEG e PNG).";
            header("Location: adicionarItem.php");
            die();
        }

        //Verifica se o arquivo está corrompido
        if(getimagesize($arquivo) == false){
            $_SESSION['erroAdicionarItem'] = "Arquivo de imagem pode estar corrompido, tente selecionar outro.";
            header("Location: adicionarItem.php");
            die();
        }

        //Verifica o tamanho do arquivo
        if($fotoTamanho > "16777216"){
            $_SESSION['erroAdicionarItem'] = "Arquivo de imagem grande demais (Tamanho suportado: 16MB).";
            header("Location: adicionarItem.php");
            die();
        }
    }

    function validaCamposEstoque($nome,$categoria,$info,$arquivo,$fotoTipo,$fotoTamanho,$fotoRecebida){

        //VALIDA TÍTULO----------------------------------------------------------------------------------------------------------
    
        //Verifica se título ta vazio ou com espaço em branco
        if(strlen($nome) == 0 || trim($nome) == ""){
            return "Por favor, digite o nome do item.";
        }
    
        //VALIDA CATEGORIA----------------------------------------------------------------------------------------------------------
    
        //Verifica se a categoria ta vazia ou com espaço em branco
        if(strlen($categoria) == 0 || trim($categoria) == ""){
            return "Por favor, escolha a categoria do item.";
        }
    
        //VALIDA INFORMAÇÕES DO PEDIDO----------------------------------------------------------------------------------------------------------
    
        //Verifica se as informações tão vazias ou com espaço em branco
        if(strlen($info) == 0 || trim($info) == ""){
            return "Por favor, digite informações sobre o item.";
        }
    
        //VALIDA IMAGEM--------------------------------------------------------------------------------------------------------------
        if($fotoRecebida == "usuario"){
            //Verifica se o arquivo foi enviado corretamente pelo método POST
            if(!is_uploaded_file($arquivo)){
                return "O arquivo de imagem não foi enviado corretamente, tente novamente.";
            }
    
            //Verifica se a extensão do arquivo é suportada (PNG, JPG E JPEG)
            if($fotoTipo != "image/png" && $fotoTipo != "image/jpg" && $fotoTipo != "image/jpeg"){
                return "Tipo de arquivo de imagem não suportado (Extensões suportadas: JPG, JPEG e PNG).";
            }
    
            //Verifica se o arquivo está corrompido
            if(getimagesize($arquivo) == false){
                return "Arquivo de imagem pode estar corrompido, tente selecionar outro.";
            }
    
            //Verifica o tamanho do arquivo
            if($fotoTamanho > "16777216"){
                return "Arquivo de imagem grande demais (Tamanho suportado: 16MB).";
            }
        }
        return "";
    }
?>