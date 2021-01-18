<!DOCTYPE html>
<html>
<head>
	<title>Ajuda</title>

	<!-- Folhas de estilo -->
    <link rel="stylesheet" type="text/css" href="style/style-ajuda.css">
    <link rel="stylesheet" type="text/css" href="style/style-warehouse.css">
    <link rel="stylesheet" type="text/css" href="style/bootstrap/css/bootstrap.min.css">

    <!-- responsividade  -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Linguagem Portugues -->
    <meta charset="utf-8">

    <!-- muda cor -->

</head>
<body > 

	    <!-- navbar -->
    <nav class="bgdark navbar navbar-dark navbar-expand-lg m-3 rounded-pill py-0 px-4" id="navbar">
        <!-- marca -->
        <div class="mr-auto d-flex align-items-center"> 
            <a class="navbar-brand" href="#">
                <img src="style/media/logo2.png" width="70" height="70" alt="" loading="lazy">
            </a>

            <a class="navbar-brand" id="logoTitle" href="home.php">Warehouse</a>
        </div>

        <!-- Links da navbar -->
        <ul class="navbar-nav d-flex align-items-center">
                
            <!-- home -->
            <li class="nav-item mr-4">
                <a class="nav-link txt20" href="home.php">Home</a>
            </li>

            <!-- artigos -->
            <li class="nav-item mr-4">
                <a class="nav-link txt20" href="artigos.php">Artigos</a>
            </li>

                <!-- ajuda -->
            <li class="nav-item mr-4">
                <a class="nav-link active txt20" href="#">Ajuda</a>
            </li>

            <!-- créditos -->
            <li class="nav-item mr-4">
                <a class="nav-link txt20" href="creditos.php">Créditos</a>
            </li>

          <li class="nav-item">
            <a class="nav-link" href="login.php">
              <img src="style/media/usericon1.png" width="70" title="Login" data-toggle="tooltip" height="70" alt="" loading="lazy">
            </a>
          </li>

        </ul>
    </nav>
    <!-- navbar end -->

    <!-- Conteudo da pagina -->
   <div class="row mt-5">
       <div class="col-3" id="lista">
           <br>
           <input class="form-control rounded-pill " id="pesquisa" type="search" placeholder="Pesquisar" aria-label="Search">
           <div class="row" id="itens">1. Onde altero minhas informações?</div><br>
           <div class="row" id="itens">2. Como crio um item?</div><br>
           <div class="row" id="itens">3. Como funciona a opção transportador?</div><br>
           <div class="row" id="itens">4. O que eu preciso para ser um transportador?</div><br>
           <div class="row" id="itens">5. Onde adiciono a minha disponibilidade?</div><br>
           <div class="row" id="itens">6. O que são os tickets?</div><br>
           <div class="row" id="itens">7. Como eu apago a minha conta?</div><br>
           <div class="row" id="itens">8. Como eu estilizo o meu perfil?</div><br>
           <div class="row" id="itens">9. Como funciona o sistema de avaliação do usuario?</div><br>
               </div>
         <div class="col">
             <div class="row">
                <div class="col" id="texto">
                    <h1 style="text-align: center;">Titulo da pergunta</h1><br>
                    <p>tenti adipiscing lectus, suspendisse auctor justo ante egestas. consectetur risus nam aliquam vehicula aliquam condimentum etiam fames aliquam, aptent non tempus platea aenean erat nunc leo justo, nunc facilisis adipiscing ac purus adipiscing fringilla 
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-3"></div>

        <!-- se o artigo foi util ou não-->
        <div class="col">
            <div class="row">
                <label>Esse Artigo foi utíl?</label>
            </div>
            <div class="row">
                <input type="button" name="" value="Sim" id="utilidade">
                <input type="button" name="" value="Não" id="utilidade2">
            </div>
        </div>



</body>
</html>
