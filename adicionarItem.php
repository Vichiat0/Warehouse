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
    $_SESSION['pagina'] = "adicionarItem";
?>
<!DOCTYPE html>
<html>
<head>
	<title>Deposite seu item - Warehouse</title>

	<!-- responsividade  -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta charset="utf-8">

    <!-- JQuery -->
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>

	<!-- Folhas de estilo -->
    <link rel="stylesheet" type="text/css" href="style/pedido.css">
    <link rel="stylesheet" type="text/css" href="style/style-warehouse.css">
    <link rel="stylesheet" type="text/css" href="style/bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="js/bootstrap/bootstrap.js"></script>
    
    <!-- <link rel="stylesheet" type="text/css" href="style/normalize.css" />
    <link rel="stylesheet" type="text/css" href="style/demo.css" />
    <link rel="stylesheet" type="text/css" href="style/component.css" /> -->

    <script>(function(e,t,n){var r=e.querySelectorAll("html")[0];r.className=r.className.replace(/(^|\s)no-js(\s|$)/,"$1js$2")})(document,window,0);</script>

    <!-- Verifica erro -->
    <script type="text/javascript" charset="utf-8" async defer>
        $(document).ready(function(){

            var imagem;
            var deuRuim = false;
            <?php 
            if(!empty($_SESSION['erroAdicionarItem'])){
                echo "$('#erroModal').modal();";
            }
            ?>

            //Valida o campo nome
            $("#title").change(function(){
                //Verifica se o nome tem menos de 3 caracteres
				if($(this).val().length < 3){
            		$("#erroNome").html("Por favor, digite o nome inteiro do item.");
            		deuRuim = true;
        		}
				//Verifica se nome ta vazio
				if($(this).val() == ""){
            		$("#erroNome").html("Por favor, digite o nome do item.");
					deuRuim = true;
                }
                if(deuRuim == true){
            		$(this).attr("class","form-control is-invalid");
					deuRuim = false;
				}
				else{
					$(this).attr("class","form-control is-valid");
				}
            })

            //Pega o número de caracteres da descrição
            $("#caracteres").html("("+ (300 - $("#input_descricao").val().length) + ")");

            //Atualiza número de caracteres da descrição
            $("#input_descricao").on('input',function(){
                $("#caracteres").html("("+ (300 - $("#input_descricao").val().length) + ")");

            })

            //Atualiza a foto escolhida pela pessoa no site
            $("#file").change(function(){
                if($(this)[0].files[0]){

                    $(".selecionaImagem").attr("class","selecionaImagem d-none");
                    $("#fotoDaora").attr("src",URL.createObjectURL($(this)[0].files[0]));
                }
                else{
                    $(".selecionaImagem").attr("class","selecionaImagem");            
                    $("#fotoDaora").attr("src","style/media/img.png");
                }
            });
        })
    </script>
</head>
<body>

    <!-- Modal de erro-->
    <div class="modal fade" id="erroModal" tabindex="-1" role="dialog" aria-labelledby="erroModalTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="erroModalTitulo">Erro</h5>
                </div>
                <div class="modal-body">
                    <?php echo $_SESSION['erroAdicionarItem']; 
                        $_SESSION['erroAdicionarItem'] = "";?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>  

<!-- Cabeçalho -->
<div class="row px-4 pt-3 bg-light" id="header">

    <!-- botão de voltar -->
	<div class="col-1" onclick="window.history.back();">
    	<span>
        	<svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="gray" xmlns="http://www.w3.org/2000/svg">
            	<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
        	</svg>
    	</span>
    </div>        

    <!-- titulo da página -->
    <div class="col">
        <p class="text-secondary text-center" id="titleHeader">Depositar item</p>
    </div>

    <div class="col-1 d-flex flex-row-reverse">
       	<a href="config.php">
       	<svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-gear" fill="gray" xmlns="http://www.w3.org/2000/svg">
  			<path fill-rule="evenodd" d="M8.837 1.626c-.246-.835-1.428-.835-1.674 0l-.094.319A1.873 1.873 0 0 1 4.377 3.06l-.292-.16c-.764-.415-1.6.42-1.184 1.185l.159.292a1.873 1.873 0 0 1-1.115 2.692l-.319.094c-.835.246-.835 1.428 0 1.674l.319.094a1.873 1.873 0 0 1 1.115 2.693l-.16.291c-.415.764.42 1.6 1.185 1.184l.292-.159a1.873 1.873 0 0 1 2.692 1.116l.094.318c.246.835 1.428.835 1.674 0l.094-.319a1.873 1.873 0 0 1 2.693-1.115l.291.16c.764.415 1.6-.42 1.184-1.185l-.159-.291a1.873 1.873 0 0 1 1.116-2.693l.318-.094c.835-.246.835-1.428 0-1.674l-.319-.094a1.873 1.873 0 0 1-1.115-2.692l.16-.292c.415-.764-.42-1.6-1.185-1.184l-.291.159A1.873 1.873 0 0 1 8.93 1.945l-.094-.319zm-2.633-.283c.527-1.79 3.065-1.79 3.592 0l.094.319a.873.873 0 0 0 1.255.52l.292-.16c1.64-.892 3.434.901 2.54 2.541l-.159.292a.873.873 0 0 0 .52 1.255l.319.094c1.79.527 1.79 3.065 0 3.592l-.319.094a.873.873 0 0 0-.52 1.255l.16.292c.893 1.64-.902 3.434-2.541 2.54l-.292-.159a.873.873 0 0 0-1.255.52l-.094.319c-.527 1.79-3.065 1.79-3.592 0l-.094-.319a.873.873 0 0 0-1.255-.52l-.292.16c-1.64.893-3.433-.902-2.54-2.541l.159-.292a.873.873 0 0 0-.52-1.255l-.319-.094c-1.79-.527-1.79-3.065 0-3.592l.319-.094a.873.873 0 0 0 .52-1.255l-.16-.292c-.892-1.64.902-3.433 2.541-2.54l.292.159a.873.873 0 0 0 1.255-.52l.094-.319z"/>
  			<path fill-rule="evenodd" d="M8 5.754a2.246 2.246 0 1 0 0 4.492 2.246 2.246 0 0 0 0-4.492zM4.754 8a3.246 3.246 0 1 1 6.492 0 3.246 3.246 0 0 1-6.492 0z"/>
		</svg>
        </a>
    </div>

</div>
<!-- /Cabeçalho -->

<!-- CARD -->
<form class="bg-light shadow-lg rounded-10 middle w-75 p-5 h-75" action="Validacao.php" method="POST" enctype="multipart/form-data" name="myForm">
    <div class="row">
        <h2 class="txt36 mx-auto font-weight-light my-3 text-secondary">Adicionar item ao seu estoque</h2>
    </div>

    <div class="row">
                
        <!-- parte esquerda -->
        <form action="Validacao.php" method="POST">

            <div class="col mr-4">

                <div class="row">    
                    <label for="title">Nome</label>
                    <input type="text" class="form-control" name="nome" id="title" aria-describedby="erroNome">
                    <small id="erroNome" class="invalid-feedback"></small>
                </div>
                <div class="row mt-3">    
                    <label for="label" class="">Categoria</label>  
                    <select class="form-control" name="categoria">
                        <option value="Instrumento">Instrumento</option>
                        <option value="Brinquedo">Brinquedo</option>
                        <option value="Vestuário">Vestuário</option>
                        <option value="Eletrodoméstico">Eletrodoméstico</option>
                        <option value="Ferramenta">Ferramenta</option>
                        <option value="Outro">Outro</option>
                    </select>
                </div>
                <div class="row mt-3">    
                    <label for="input_descricao" class="">Informações sobre o item</label>
                    <!--  <input type="text" class="form-control" name="descri" id="descricao"> -->
                    <textarea class="form-control" maxlength="300" name="info" id="input_descricao"></textarea>
                    <a id="caracteres">(300)</a>
                </div>
            </div>

        </form>

        <!-- parte direita -->
        <div class="col">
            <div class="row mt-5">    
                <div class="form-check mx-auto">
                    <input class="form-check-input" type="radio" id="radioTroca" name="radio_pedido" value="option1" checked>
                    <label class="form-check-label" for="radioTroca">Quero trocar</label>
                </div>
                <div class="form-check mx-auto">
                    <input class="form-check-input" type="radio" id="radioDoacao" name="radio_pedido" value="option2">
                    <label class="form-check-label" for="radioDoacao" id="input_doacao">Quero doar</label>
                </div>
            </div>
                    
            <div class="row mt-2">
                <div class="box mx-auto">
                    <center>
                    <input type="file" accept='.jpg,.jpeg,.png' name="arquivoImagem" id="file" class="d-none inputfile inputfile-4" data-multiple-caption="{count} files selected" multiple />
                    <label class="cursor-pointer" for="file"><img src="style/media/img.png" id="fotoDaora" height="150" width="150"><br><span class="selecionaImagem">Selecione uma imagem&hellip;</span></label>
                    </center>
                </div>
            </div>
            <div class="row">
                <button type="submit" class="btn btn-primary w-50 py-2 mx-auto">Criar item</button>
            </div>          
        </div>
    </div>
</form>
</body>
</html>