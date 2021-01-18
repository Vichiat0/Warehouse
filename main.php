<!-- Verifica se o usuário tá logado -->
<?php
	session_start();
    if(!empty($_SESSION['usuario'])){
        $usuario = $_SESSION['usuario'];
    }
    else{
        header("Location: home.php");
        die();
    }
    //Inicia BD 
    include_once("Conexao_BD.php");
    $_SESSION['pagina'] = "main";
    $queryPesquisa = "SELECT * FROM usuario WHERE email = '$usuario' LIMIT 1";
    $resultUsuario = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
    if(empty($resultUsuario)){
        $_SESSION['erroHome'] = "Não foi possível acessar os dados do perfil, por favor, tente reconectar-se.";
		header("Location: home.php");
		die();
    }

    //Verifica se usuário tem notificações
    $resultNotificacao = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "'"));
    if(!empty($resultNotificacao)){
        $numNotificacaoChat = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='chat'"));
        if($numNotificacaoChat['COUNT(*)'] < 1){
            $numNotificacaoChat = null;
        }
        $numNotificacaoTransacao = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo!='chat'"));
        if($numNotificacaoTransacao['COUNT(*)'] < 1){
            $numNotificacaoTransacao = null;
        }
    }
    else{
        $numNotificacaoChat = null;
        $numNotificacaoTransacao = null;
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bem vindo ao Warehouse</title>

    <!-- Responsividade  -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta charset="utf-8">

    <!-- Folhas de estilo -->
    <link rel="stylesheet" type="text/css" href="style/style-main.css">
    <link rel="stylesheet" type="text/css" href="style/style-warehouse.css">
    <link rel="stylesheet" type="text/css" href="style/bootstrap/css/bootstrap.css">

    <!-- Kit de icones -->
    <script src="https://kit.fontawesome.com/c40cc3d328.js" crossorigin="anonymous"></script>

    <!-- javascripts do bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script type="text/javascript" src="style/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/parallax.js/1.4.2/parallax.min.js"></script>


</head>
<body>

    <!-- Modal de erro-->
    <div class="modal fade" id="erroModal" tabindex="-1" role="dialog" aria-labelledby="erroModalTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="erroModalTitulo">Erro</h5>
                </div>
                <div class="modal-body" id="msgErro">
                    <?php echo $_SESSION['erroMain']; 
                        $_SESSION['erroMain'] = "";?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>



<!-- NAV -->
<div class="row fixed-top d-flex align-items-top m-3">
        


    <!-- Menu Hamburger -->
    <div class="col-3">
        <div class="row">
            <nav class="navbar navbar-light mt-2 w-100">

                <button class="border-0 navbar-toggler bg-light rounded-lg" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" style="outline: none;" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span><?php if(!empty($numNotificacaoChat)){ echo "<sup><span class='badge badge-pill badge-danger'>!</span></sup>"; } ?>
                </button>

                <div class="collapse navbar-collapse p-4 mt-2 border shadow bg-light rounded-lg" id="navbarNav">
                    <ul class="navbar-nav">

                        <a class="navbar-brand mx-auto mb-1" id="logoTitle" href="main.php">Warehouse</a>

                        <li class="nav-item ml-2">
                            <a class="nav-link" href="adicionarItem.php">Adicionar Item</a>
                        </li>
                        <li class="nav-item ml-2">
                            <a class="nav-link" href="estoque.php">Estoque</a>
                        </li>
                        <li class="nav-item ml-2">
                            <a class="nav-link" href="chat.php">Chat<?php if(!empty($numNotificacaoChat)){ echo "<sup><span class='badge badge-pill badge-danger'>" . $numNotificacaoChat['COUNT(*)'] . "</span></sup>"; } ?></a>
                        </li>
                        <hr>
                        <li class="nav-item ml-2">
                            <a class="nav-link" href="config.php">Configurações</a>
                        </li>
                        <li class="nav-item ml-2">
                            <a class="nav-link" href="artigos.php#share">Compartilhar</a>
                        </li>
                        <li class="nav-item ml-2">
                            <a class="nav-link" href="creditos.php">Créditos</a>
                        </li>
                        <a href="Validacao.php">
                            <button class="btn btn-danger w-100 mt-3" id="logout">Logout</button>
                        </a>
                    </ul>
                </div>

            </nav>
        </div>
    </div>
    <!-- /Menu Hamburger -->






    <!-- Barra de Pesquisa -->
    <div class="col mt-3">
        <div class="row">

                <input class="form-control rounded-pill w-75" id="pesquisa" type="search" placeholder="Pesquise o item" aria-label="Search">

                <button id="btnPesquisa" class="btn ml-1 rounded-circle" type="button">
                    <svg width="1em" height="1.5em" viewBox="0 0 16 16" class="bi bi-search" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"/>
                        <path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
                    </svg>
                </button>

        </div>
    </div>
    <!-- /Barra de Pesquisa -->





    <!-- Icone de Perfil -->
    <div class="col-3">
        <div class="row d-flex flex-row-reverse">
             <!-- icone de perfil -->
            <a class="align-middle" href="perfil.php">
            <img class="" <?php if($resultUsuario['fotoTipo'] == "site"){ 
                                    echo "src='style/media/usericon" . bindec($resultUsuario['fotoPerfil']) . ".png'";
                                }
                                else{
                                    echo "src='data:" . $resultUsuario['fotoTipo'] . ";base64," . base64_encode($resultUsuario['fotoPerfil']) . "'";
                                }?> title="Perfil" data-toggle="tooltip" width="70" height="70">
            </a><?php if($numNotificacaoTransacao != null){ echo "<sup><span class='badge badge-pill badge-danger'>!</span></sup>"; } ?>
        </div>
    </div>
    <!-- /Icone de Perfil -->



</div>
<!-- /NAV -->



<div class="my-4">
    <h1 style="visibility: hidden;">.</h1>
</div>



<div class="">
    
<h5 class="ml-4 text-secondary">Resultados de pesquisa</h5>
<div class="p-2 mb-5 rounded-lg bg-dark">
    <div class="row">
        <div class="col">
            <div class="categorias filtro d-flex align-items-center justify-content-center">
                <p class="txt-white txt12">Instrumento</p>
            </div>
        </div>

        <div class="col">
            <div class="categorias filtro d-flex align-items-center justify-content-center">
                <p class="txt-white txt12">Vestuário</p>
            </div>
        </div>
        <div class="col">
            <div class="categorias filtro d-flex align-items-center justify-content-center">
                <p class="txt-white txt12">Brinquedo</p>
            </div>
        </div>
        <div class="col">
            <div class="categorias filtro d-flex align-items-center justify-content-center">
                <p class="txt-white txt12">Ferramenta</p>
            </div>
        </div>
        <div class="col">
            <div class="categorias filtro d-flex align-items-center justify-content-center">
                <p class="txt-white txt12">Eletrodoméstico</p>
            </div>
        </div>
        <div class="col">
            <div class="categorias filtro d-flex align-items-center justify-content-center">
                <p class="txt-white txt12">Outro</p>
            </div>
        </div>
        <div class="col">
            <div class="intuito filtro d-flex align-items-center justify-content-center">
                <p class="txt-white txt12">Troca</p>
            </div>
        </div>
        <div class="col">
            <div class="intuito filtro d-flex align-items-center justify-content-center">
                <p class="txt-white txt12">Doação</p>
            </div>
        </div>
        <div class="col">
            <div class="categorias filtro d-flex align-items-center justify-content-center">
                <p class="txt-white txt12">Desabilitar filtro</p>
            </div>
        </div>

    </div>
    
</div>

<!-- DECK -->
<div id="resultadoPesquisa" class='bg-light px-5 my-5 mx-auto'>
</div>
<!-- /DECK -->

<!-- Paginação -->
<nav id="paginacao" class=""></nav>

<!-- busca -->
<div id="pesquisaVazia" class='m-5 d-none'>

    <h4 class="lead text-secondary text-center">
    <svg width="1.0625em" height="1em" viewBox="0 0 17 16" class="bi bi-exclamation-triangle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 5zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
    </svg>
    Não houve resultados na pesquisa.
    </h4>

</div> 
<!-- /busca -->

<script>
    $(function(){

        // Mostra erro
<?php   if(!empty($_SESSION['erroMain'])){?>
            $('#erroModal').modal();
<?php   } ?>

        var itemPost = new FormData();
        indicePesquisa = 0;
        itemPost.append("indicePesquisa",1);
        itemPost.append("novaPesquisa",'n');
        itemPost.append("categoria",'');
        itemPost.append("intuito",'');
        itemPost.append("operacao","read");
        itemPost.append("tipoPesquisa","geral");
        pesquisaFeita = true;
        pesquisado = false;
        minPag = 0;
        maxPag = 0;
        numMaxPag = 0;

        function limpaPesquisa(){
            $("#pesquisaVazia").attr("class","");
            $("#paginacao").attr("class","d-none");
            $("#resultadoPesquisa").attr("class","d-none");
            $("#numeracaoPaginas").remove();
            $("#itensPesquisa").remove();
        }

        function pesquisaItem(){
            pesquisaFeita = false;
            itemPost.append("indicePesquisa",parseInt(indicePesquisa,10));
            itemPost.append("stringChave",$("#pesquisa").val());
            $.ajax({
                method: 'POST',
                url: 'rudItem.php',
                data: itemPost,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (jason){
                    if(jason.resultado == 'true'){
                        $("#pesquisaVazia").attr("class","d-none");
                        if(jason.paginas > 0){
                            numMaxPag = jason.paginas;
                        }
                        $("#numeracaoPaginas").remove();
                        $("#paginacao").append("<ul id='numeracaoPaginas' class='pagination justify-content-center'><li class='page-item mudaPagina' id='anterior'><a class='page-link' style='cursor:pointer;'><span aria-hidden='true'>&laquo;</span><span class='sr-only'>Previous</span></a></li>");
                        minPag = indicePesquisa - 2;
                        maxPag = indicePesquisa + 2;
                        if(indicePesquisa < 3 && numMaxPag >= 5){
                            maxPag = 5;
                        }
                        if(indicePesquisa > numMaxPag - 2){
                            minPag = numMaxPag - 4;
                        }
                        for(pagina = minPag; pagina <= maxPag; pagina++){
                            if(pagina <= numMaxPag && pagina > 0){
                                if(pagina == indicePesquisa){
                                    $("#numeracaoPaginas").append("<li class='page-item active paginas' style='cursor:pointer;'><a class='page-link'>"+pagina+"</a></li>");
                                }
                                else{
                                    $("#numeracaoPaginas").append("<li class='page-item paginas' style='cursor:pointer;'><a class='page-link'>"+pagina+"</a></li>");
                                }
                            }
                        }
                        $("#numeracaoPaginas").append("<li class='page-item'><a class='page-link mudaPagina' id='proxima' style='cursor:pointer;'><span aria-hidden='true'>&raquo;</span><span class='sr-only'>Next</span></a></li>");
                        $("#paginacao").append("</ul>");
                        $("#paginacao").attr("class","");
                        itemPost.append("novaPesquisa",'n');
                        $("#itensPesquisa").remove();
                        $("#resultadoPesquisa").append("<form id='itensPesquisa' action='pessoa.php' method='POST' class='row d-flex justify-content-center'><input type='hidden' id='itemId' name='Itsid'>");
                        for(contItem = 0; jason[contItem]; contItem++){
                            $("#itensPesquisa").append("<a class='btn m-3 p-0 item' id='"+jason[contItem].id+"'><div class='cards shadow-lg rounded-lg bg-light p-2'><div class='row'><div class='col-5'><img src='data:"+jason[contItem].imagemTipo+";base64,"+jason[contItem].imagem+"' height='135' width='130' class='rounded-lg'></div><div class='col'><p class='lead mt-2 text-center'>"+jason[contItem].nome+"</p><p class='txt-primary text-center mt-2 "+jason[contItem].categoria+"'>"+jason[contItem].categoria+"</p><p id='displayIntuito"+contItem+"' class='text-center mt-2'></p></div></div></div></a>");
                            if(jason[contItem].intuito == "troca"){
                                $("#displayIntuito"+contItem).html("(Troca)");
                            }
                            else{
                                $("#displayIntuito"+contItem).html("(Doação)");
                            }
                        }
                        $("#resultadoPesquisa").append("</form>");
                        $("#resultadoPesquisa").attr("class","bg-light px-5 my-5 mx-auto");
                    }
                    else{
                        limpaPesquisa();
                    }
                    pesquisaFeita = true;
                },  
                error: function (error){
                    alert(error.responseText);
                }      
            });
        }

        function novaPesquisa(){
            itemPost.append("novaPesquisa",'s');
            indicePesquisa = 1;
            pesquisaItem();
        }

        $("#pesquisa").on('keypress',function(tecla) {
            if(pesquisaFeita == true){
                if(tecla.which == 13){
                    if($("#pesquisa").val().trim() != "") {
                        itemPost.append("categoria",'');
                        novaPesquisa();
                    }
                    else{
                        limpaPesquisa();
                    }
                    pesquisado = true;
                }
            }
        });

        $('#btnPesquisa').click(function(){ 
            if(pesquisaFeita == true){
                if($("#pesquisa").val().trim() != "") {
                    itemPost.append("categoria",'');
                    novaPesquisa();
                }
                else{
                    limpaPesquisa();
                }
                pesquisado = true;
            }
        });

        $('.next').click(function(){ 
            $('.carousel').carousel('next');
        });

        $('.prev').click(function(){ 
            $('.carousel').carousel('prev');
        });

        //Filtro de categoria
        $(".categorias").click(function(){
            if(pesquisaFeita == true){
                if($("#pesquisa").val().trim() != "" && pesquisado == true) {
                    if($(this).find("p").html() == "Desabilitar filtro"){
                        itemPost.append("categoria","");
                        itemPost.append("intuito","");
                    }
                    else{
                        itemPost.append("categoria",$(this).find("p").html());
                    }
                    novaPesquisa();
                }
            }
        })

        //Filtro de tipo de transação
        $(".intuito").click(function(){
            if(pesquisaFeita == true){
                if($("#pesquisa").val().trim() != "" && pesquisado == true) {
                    itemPost.append("intuito",$(this).find("p").html());
                    novaPesquisa();
                }
            }
        })

        //Setinhas da paginação
        $(document).on('click','.mudaPagina',function(){
            if(pesquisaFeita == true){
                if($(this).attr("id") == "anterior"){
                    if((indicePesquisa - 1) > 0){
                        indicePesquisa--;
                        pesquisaItem();
                    }
                }
                else{
                    if((indicePesquisa + 1) <= numMaxPag){
                        indicePesquisa++;
                        pesquisaItem();
                    }
                }
            }
        });

        //Números da paginação
        $(document).on('click','.paginas',function(){
            if(pesquisaFeita == true){
                indicePesquisa = parseInt($(this).find("a").html(),10);
                pesquisaItem();
            }
        });

        $(document).on('click','.item',function(){
            $("#itemId").val($(this).attr("id"));
            $("#itensPesquisa").submit();
        });

        $(".filtro").hover(function(){$(this).css("background-color","#fe5f55");},function(){$(this).css("background-color","#343a40");});
    });
</script>
</body>
</html>