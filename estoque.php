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
    include_once("Conexao_BD.php");
    $queryPesquisa = "SELECT * FROM usuario WHERE email = '$usuario' LIMIT 1";
    $resultUsuario = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
    if(empty($resultUsuario)){ 
        $_SESSION['erroHome'] = "Não foi possível acessar os dados do usuário, tente reconectar-se";
        header("Location: home.php");
        die();
    }

    //Recebe o número de páginas do estoque do usuário
	$queryPesquisa = "SELECT COUNT(*) FROM item WHERE cpfcnpj = '" . $resultUsuario['cpfcnpj'] . "'";
    $resultNumPag = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
    
    $queryPesquisa = "SELECT * FROM item WHERE cpfcnpj = '" . $resultUsuario['cpfcnpj'] . "' AND emUso='n' LIMIT 0,20";

    $_SESSION['pagina'] = "adicionarItem";
?>
<!DOCTYPE html>
<html>
<head>
	<title>Veja seu estoque - Warehouse</title>

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
    

    <script>(function(e,t,n){var r=e.querySelectorAll("html")[0];r.className=r.className.replace(/(^|\s)no-js(\s|$)/,"$1js$2")})(document,window,0);</script>

</head>
<body>

    <!-- Modal de erro-->
    <div class="modal fade" id="erroModal" tabindex="-1" role="dialog" aria-labelledby="erroModalTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="erroModalTitulo">Erro</h5>
                </div>
                <div class="modal-body"id="erroMsg">
                    <?php echo $_SESSION['erroEstoque']; 
                        $_SESSION['erroEstoque'] = "";?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal de edição -->
    <div class="modal fade" id="itemModal" tabindex="-1" role="dialog" aria-labelledby="" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">


                <div class="modal-header">
                    <h5 class="modal-title" id="erroModalTitulo">Alterar dados do item</h5>
                </div>


                <div class="modal-body">
                    <form>

                        <div class="form-group d-flex justify-content-center">
                            <div class="form-check mx-4 d-inline">
                                <input class="form-check-input" type="radio" id="radioTroca" name="itemIntuito" value="troca">
                                <label class="form-check-label" for="radioTroca">Quero trocar</label>
                            </div>
                            <div class="form-check mx-4 d-inline">
                                <input class="form-check-input" type="radio" id="radioDoacao" name="itemIntuito" value="doacao">
                                <label class="form-check-label" for="radioDoacao" id="input_doacao">Quero doar</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="exampleInputEmail1">Nome</label>
                            <input type="text" class="form-control" id="itemNome">
                        </div>
                        
                        <!-- depois vcs trocam esse campo de categorias para o select do combo box -->
                        <div class="form-group">
                            <label for="label" class="">Categoria</label>  
                            <select id="itemCategoria" class="form-control" name="categoria">
                                <option value="Instrumento">Instrumento</option>
                                <option value="Brinquedo">Brinquedo</option>
                                <option value="Vestuário">Vestuário</option>
                                <option value="Eletrodoméstico">Eletrodoméstico</option>
                                <option value="Ferramenta">Ferramenta</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="input_descricao" class="">Informações sobre o item</label>
                            <textarea id="itemDescricao" class="form-control" maxlength="300" name="info" id="input_descricao"></textarea>
                        </div>

                        <div class="form-group m-0 p-0 d-flex justify-content-center">
                            <center>
                            <input type="file" accept='.jpg,.jpeg,.png' name="arquivoImagem" id="file" class="d-none inputfile inputfile-4" data-multiple-caption="{count} files selected" multiple />
                            <label class="cursor-pointer" for="file"><img id="itemImagem" height="150" width="150"><br><span class="selecionaImagem d-none">Selecione uma imagem&hellip;</span></label>
                            </center>
                        </div>



                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light mr-auto" data-dismiss="modal">Cancelar</button>
                    <button id="btnExclui" type="button" class="btn btn-danger" data-dismiss="modal">Excluir</button>
                    <button id="btnAltera" type="button" class="btn btn-primary" data-dismiss="modal">Alterar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de excluir item-->
    <div class="modal fade" id="excluiModal" tabindex="-1" role="dialog" aria-labelledby="excluiModalTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="excluiModalTitulo">Deseja mesmo apagar esse item?</h5>
                </div>
                <div class="modal-footer">
                    <button id="confirmExcluiItem" type="button" class="btn btn-danger" data-dismiss="modal">Sim</button>
                    <button type="button" class="btn btn-danger voltaItem" data-dismiss="modal">Não</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de conclusão de alteração-->
    <div class="modal fade" id="sucessoModal" tabindex="-1" role="dialog" aria-labelledby="excluiModalTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="excluiModalTitulo">Sucesso</h5>
                </div>
                <div class="modal-body">
                    Item alterado com sucesso!
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary voltaItem" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <input type="hidden" id='idUsuario' value='<?php echo $usuario; ?>'>
    <input type="hidden" id='idPaginas' value='<?php echo ceil($resultNumPag['COUNT(*)']/20); ?>'>

<!-- Cabeçalho -->
<div class="row px-4 pt-3 bg-light" id="header">

    <!-- botão de voltar -->
	<div class="col-1">
		<a style="cursor: pointer" href="main.php">
    	<span>
        	<svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="gray" xmlns="http://www.w3.org/2000/svg">
            	<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
        	</svg>
    	</span>
    	</a>
    </div>        

    <div class="col">
        <p class="text-secondary text-center" id="titleHeader">Seu estoque</p>
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







<!-- CORPO DO SITE -->

<!-- alerta de deck vazio -->
<div id="estoqueVazio"<?php if(!empty(mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa)))){ 
                                echo "class='d-none'"; 
                            }
                            else{
                                echo "class='m-5'";
                            } ?>>


    <h4 class="lead text-secondary text-center">
    <svg width="1.0625em" height="1em" viewBox="0 0 17 16" class="bi bi-exclamation-triangle-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5a.905.905 0 0 0-.9.995l.35 3.507a.552.552 0 0 0 1.1 0l.35-3.507A.905.905 0 0 0 8 5zm.002 6a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
    </svg>
    Estoque vazio, adicione novos itens!
    </h4>

</div>
<!-- /alerta de deck vazio -->


        <!-- DECK -->
        <div id="estoque" <?php if(empty(mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa)))){ 
                                    echo "class='d-none'"; 
                                }
                                else{
                                    echo "class='bg-light px-5 my-5 mx-auto'";
                                } ?>>
            <center> <h5>Clique no item para ver suas informações</h5> </center>
            <div id='itensPesquisa' class="row d-flex justify-content-center">


<?php           if(!empty(mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa)))){
                    $resultQuery = mysqli_query($conn,$queryPesquisa);
                    while($resultItens = mysqli_fetch_assoc($resultQuery)){ ?>

                        <!-- CARD -->
                        <a id='<?php echo $resultItens['id'] ?>' class="btn m-3 p-0 item">

                        <div class="cards shadow-lg rounded-lg bg-light p-2">
                            <div class="row">
                                <div class="col-5">
                                    <img id="displayImagem" <?php echo "src='data:" . $resultItens['imagemTipo'] . ";base64," . base64_encode($resultItens['imagem']) . "'"; ?> height="135" width="130" class="rounded-lg">
                                </div>
                                <div class="col">
                                    <p id="displayNome" class="lead mt-2 text-center"><?php echo $resultItens['nome'] ?></p>
                                    <p id="displayCategoria" class="txt-primary text-center mt-2"><?php echo $resultItens['categoria'] ?></p>
                                    <p id="displayIntuito" class="text-center mt-2"><?php   if($resultItens['intuito'] == "doacao"){
                                                                            echo "(Doação)";
                                                                        }
                                                                        else{
                                                                            echo "(Troca)";
                                                                        } ?></p>
                                </div>
                            </div>
                        </div>

                        </a>
                        <!-- /CARD -->
<?php               }
                } ?>
            </div>
        </div>
<!-- /DECK -->

<!-- Paginação -->
                        
<nav id="paginacao" <?php   if(!empty(mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa)))){ 
                                echo "class='m-5'"; 
                            }
                            else{
                                echo "class='d-none'";
                            } ?>>
	<ul id='numeracaoPaginas' class='pagination justify-content-center'>
		<li class='page-item mudaPagina' id='anterior'>
			<a class='page-link' style='cursor:pointer;'>
				<span aria-hidden='true'>&laquo;</span><span class='sr-only'>Previous</span>
			</a>
		</li>
		<li class='page-item paginas active' style='cursor:pointer;'>
			<a class='page-link'>1</a>
		</li>
<?php	for($pagina = 2; $pagina <= 5; $pagina++){
			if($pagina <= ceil($resultNumPag['COUNT(*)'] / 20)){ ?>
				<li class='page-item paginas' style='cursor:pointer;'><a class='page-link'><?php echo $pagina; ?></a></li>
<?php		}
		} ?>
		<li class='page-item'>
			<a class='page-link mudaPagina' id='proxima' style='cursor:pointer;'>
				<span aria-hidden='true'>&raquo;</span><span class='sr-only'>Next</span>
			</a>
		</li>
	</ul>
</nav>
<!-- /Paginação -->

<!-- BOTÃO DE ADICIONAR -->
<div class="row mb-5">
    <a href="adicionarItem.php" class="btn btn-primary rounded-pill py-2 mx-auto w-25" style="font-size: 20px;">Adicionar</a>
</div>
<!-- /BOTÃO DE ADICIONAR -->



<script type="text/javascript" charset="utf-8" async defer>
        $(document).ready(function(){

            var imagem;
            var deuRuim = false;
            var itemPost = new FormData();
            var id;
		    itemPost.append("usuario",$("#idUsuario").val());
            itemPost.append("tipoUpdate","geral");
		    var numMaxPag = parseInt($("#idPaginas").val(),10);
		    var pesquisaFeita = true;
		    var indicePesquisa = 1;
		    var minPag = 0;
            var maxPag = 0;

		    function pesquisaItem(){
                pesquisaFeita = false;
                itemPost.append("operacao","read");
                itemPost.append("tipoPesquisa","usuario");
                itemPost.append("indicePesquisa",parseInt(indicePesquisa,10));
                $.ajax({
                    method: 'POST',
                    url: 'rudItem.php',
                    data: itemPost,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (jason){
                        $("#itensPesquisa").remove();
                        $("#estoque").append("<div id='itensPesquisa' class='row d-flex justify-content-center'>");
                        for(contItem = 0; jason[contItem]; contItem++){
                            $("#itensPesquisa").append("<a class='btn m-3 p-0 item' id='"+jason[contItem].id+"'><div class='cards shadow-lg rounded-lg bg-light p-2'><div class='row'><div class='col-5'><img src='data:"+jason[contItem].imagemTipo+";base64,"+jason[contItem].imagem+"' height='135' width='130' class='rounded-lg'></div><div class='col'><p class='lead mt-2 text-center'>"+jason[contItem].nome+"</p><p class='txt-primary text-center mt-2'>"+jason[contItem].categoria+"</p><p id='displayIntuito"+contItem+"' class='text-center mt-2'></p></div></div></div></a>");
                            if(jason[contItem].intuito == "troca"){
                                $("#displayIntuito"+contItem).html("(Troca)");
                            }
                            else{
                                $("#displayIntuito"+contItem).html("(Doação)");
                            }
                            $("#estoque").append("</div>");
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

                        pesquisaFeita = true;
                    },  
                    error: function (error){
                        alert(error.responseText);
                    }      
                });
            }

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

            //Abre o card do item e recupera as informações no BD
            $(document).on('click','.item',function(){
                id = $(this).attr("id");
                itemPost.append("id",id);
                itemPost.append("operacao","read");
                itemPost.append("tipoPesquisa","item");
                $.ajax({
                    method: 'POST',
                    url: 'rudItem.php',
                    data: itemPost,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (jason){
                        switch(jason.erro){
                            case 'item':
                                $("#erroMsg").html(jason.msg);
                                $("#erroModal").modal();
                            break;
                            case 'nenhum':
                                $("#itemNome").val(jason.nome);
                                $("#itemCategoria").val(jason.categoria);
                                $("#itemDescricao").val(jason.descricao);
                                $("#itemImagem").attr("src",jason.imagem);
                                $("#file").val(null);
                                $('#itemModal').modal();
                                if(jason.intuito == "doacao"){
                                    $("#radioDoacao").prop("checked",true).checkboxradio("refresh");
                                }
                                else{
                                    $("#radioTroca").prop("checked",true).checkboxradio("refresh");
                                }
                            break;
                        }
                    }   
                });
            });

            //Atualiza a foto escolhida pela pessoa no site
            $("#file").change(function(){
                if($(this)[0].files[0]){
                    $(".selecionaImagem").attr("class","selecionaImagem d-none");
                    $("#itemImagem").attr("src",URL.createObjectURL($(this)[0].files[0]));
                }
                else{
                    $(".selecionaImagem").attr("class","selecionaImagem");            
                    $("#itemImagem").attr("src","style/media/img.png");
                }
            });

            //Altera o item
            $('#btnAltera').click(function(){
                itemPost.append("operacao","update");
                itemPost.append("nome",$("#itemNome").val());
                itemPost.append("categoria",$("#itemCategoria").val());
                itemPost.append("descricao",$("#itemDescricao").val());
                itemPost.append("imagemArquivo","");
                if($("#file")[0].files[0]){
                    itemPost.append("imagemArquivo",$("#file")[0].files[0]);
                }
                itemPost.append("intuito",$("input[name='itemIntuito']:checked").val());
                $.ajax({
                    method: 'POST',
                    url: 'rudItem.php',
                    data: itemPost,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (jason){
                        switch(jason.erro){
                            case 'item':
                                $("#erroMsg").html(jason.msg);
                                $("#erroModal").modal();
                            break;
                            case 'nenhum':
                                $("#sucessoModal").modal();
                            break;
                        }
                    } ,   
                    error: function(erro,error,errao){
                        alert(erro.responseText+" "+error+" "+errao);
                    }   
                });
            });

            $('#sucessoModal').on('hidden.bs.modal', function (e) {
                $("#"+id).find("#displayNome").html($("#itemNome").val());
                $("#"+id).find("#displayCategoria").html($("#itemCategoria").val());
                if($("input[name='itemIntuito']:checked").val() == "troca"){
                    $("#"+id).find("#displayIntuito").html("(Troca)");
                }
                else{
                    $("#"+id).find("#displayIntuito").html("(Doação)");
                }
                if($("#file")[0].files[0]){
                    $("#"+id).find("#displayImagem").attr("src",URL.createObjectURL($("#file")[0].files[0]));
                }
            });

            //Abre o modal de excluir item
            $('#btnExclui').click(function(){
                $("#excluiModal").modal();
            });

            //Confirma a exclusão de item
            $('#confirmExcluiItem').click(function(){
                itemPost.append("operacao","delete");
                $.ajax({
                    method: 'POST',
                    url: 'rudItem.php',
                    data: itemPost,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (jason){
                        switch(jason.erro){
                            case 'usuario':
                                window.Location.href = "https://localhost/TCC/home.php";
                            break;
                            case 'item':
                                $("#erroMsg").html(jason.msg);
                                $("#erroModal").modal();
                            break;
                            case 'nenhum':
                                $("#"+id).remove();
                                if(jason.vazio == "s"){
                                    $("#estoque").attr("class","d-none");
                                    $("#estoqueVazio").attr("class","m-5");
                                }
                                else{
                                    if(!$(document).find(".item")[0]){
                                        indicePesquisa--;
                                        numMaxPag--;
                                        pesquisaItem();
                                    }
                                }
                            break;
                        }
                    }        
                });
            });

            //Cancela a exclusão de item
            $('.voltaItem').click(function(){
                $("#itemModal").modal();
            });

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
            });

            //Pega o número de caracteres da descrição
            $("#caracteres").html("("+ (300 - $("#input_descricao").val().length) + ")");

            //Atualiza número de caracteres da descrição
            $("#input_descricao").on('input',function(){
                $("#caracteres").html("("+ (300 - $("#input_descricao").val().length) + ")");

            });

            //Atualiza a foto escolhida pela pessoa no site
            $("#file").change(function(){
                if($(this)[0].files[0]){
                    $("#fotoDaora").attr("src",URL.createObjectURL($(this)[0].files[0]));
                }
                else{
                    $("#fotoDaora").attr("src","style/media/img.png");
                }
            });
        })
    </script>
</body>
</html>