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

	$_SESSION['pagina'] = "outraPessoa";

	//Recebe o item clicado
	$outroUsuarioItem = $_POST['Itsid'];
	$queryPesquisa = "SELECT cpfcnpj FROM item WHERE id = $outroUsuarioItem LIMIT 1";
	$resultUsuarioItem = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
	
	//Recebe o usuário dono do item
    $queryPesquisa = "SELECT * FROM usuario WHERE cpfcnpj = '" . $resultUsuarioItem['cpfcnpj'] . "' LIMIT 1";
    $resultOutroUsuario = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
    if(empty($resultOutroUsuario)){ 
		$_SESSION['erroMain'] = "Não foi possível acessar os dados do outro usuário.<br>
								 Código de erro: " . mysqli_errno($conn);
        header("Location: main.php");
        die();
	}

	//Recebe o número de páginas do estoque do usuário
	$queryPesquisa = "SELECT COUNT(*) FROM item WHERE cpfcnpj = '" . $resultOutroUsuario['cpfcnpj'] . "'";
	$resultNumPag = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));

	//Prepara a query pros itens
	$queryPesquisa = "SELECT * FROM item WHERE cpfcnpj = '" . $resultOutroUsuario['cpfcnpj'] . "' AND emUso='n' ORDER BY(CASE WHEN id=$outroUsuarioItem THEN 1 ELSE 2 END) LIMIT 0,20";

?>
<!DOCTYPE html>
<html>
<head>
	<title>Nome da pessoa</title>

	<!-- responsividade  -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta charset="utf-8">

    <!-- JQuery -->
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>

	<!-- Folhas de estilo -->
    <link rel="stylesheet" type="text/css" href="style/perfil.css">
    <link rel="stylesheet" type="text/css" href="style/style-warehouse.css">
    <link rel="stylesheet" type="text/css" href="style/bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="js/bootstrap/bootstrap.js"></script>
</head>
<body>

<input type="hidden" id='idOutroUsuario' value='<?php echo $resultOutroUsuario['email']; ?>'>
<input type="hidden" id='idPaginas' value='<?php echo ceil($resultNumPag['COUNT(*)']/20); ?>'>





<!-- Cabeçalho -->
<div class="row px-4 pt-3 bg-light" id="header">

    <!-- botão de voltar -->
	<div class="col-1">
		<a style="cursor: pointer" onclick="window.history.back();">
    	<span>
        	<svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="gray" xmlns="http://www.w3.org/2000/svg">
            	<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
        	</svg>
    	</span>
    	</a>
    </div>        

    <div class="col">
        <p class="text-secondary text-center" id="titleHeader">Perfil de <?php echo $resultOutroUsuario['nome'] ?></p>
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




<!-- BACKGROUND DA PÁGINA -->
	<img <?php echo"src='style/media/presetbg" . $resultOutroUsuario['fotoBg'] . ".jpg'"; ?> class="bg-dark position-absolute" id="background">
<!-- BACKGROUND DA PÁGINA -->



	<div class="container justify-content-center d-flex mt-5">
		
			<div class="col p-5 m-5">



				<!-- foto o nome -->
				<div class="row mb-5">

					<div class="mx-auto">
					<center>
				
						<!-- FOTO DE PERFIL -->
						<img <?php	if($resultOutroUsuario['fotoTipo'] == "site"){ 
										echo "src='style/media/usericon" . bindec($resultOutroUsuario['fotoPerfil']) . ".png'";
					 				}
					 				else{
										echo "src='data:" . $resultOutroUsuario['fotoTipo'] . ";base64," . base64_encode($resultOutroUsuario['fotoPerfil']) . "'";
					 				} ?> class="rounded-circle" height="150" width="150">
				
						<!-- NOME -->
						<h3 class="text-light font-weight-light"><?php echo $resultOutroUsuario['nome'] ?></h3>

					</center>
					</div>

				</div>
				<!-- /foto e nome -->





				<!-- informações da pessoais -->
				<div class="row p-5 mb-5 shadow-lg rounded-lg bg-light">
					<div>
					<p class="txt36">Informações Pessoais</p>
						<p class="">Nome: <?php echo $resultOutroUsuario['nome'] ?></p>
						<p class="">Email: <?php echo $resultOutroUsuario['email'] ?></p>
						<p class="">Descrição: <?php 	if(empty($resultUsuario['descricao'])){
        													echo " (Sem descrição)";
        													}
                                						else{
                                    						echo $resultUsuario['descricao'];
                                						} ?></p>
					</div>
				</div>
				<!-- /informações pessoais -->


													

				<!-- estoque -->
				<div class="row p-5 shadow-lg rounded-lg bg-dark d-flex  justify-content-center">
					<div>
						<center><p class="txt36 text-light">Estoque</p>
						<p class="txt20 text-light">(Clique no item que se interessar)</p></center>

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
    							Estoque vazio, esse usuário ainda não adicionou itens!
    						</h4>

						</div>
						<!-- /alerta de deck vazio -->


						<!-- CARD -->
					<div id="resultadoPesquisa">
						<form id="itensPesquisa" method="POST" action="transacao.php" style="height:480px; width:500px; overflow-y: auto">
							<input type="hidden" id="itemId" name="Itsid">

<?php           			if(!empty(mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa)))){
                    			$resultQuery = mysqli_query($conn,$queryPesquisa);
								while($resultItens = mysqli_fetch_assoc($resultQuery)){ ?>
                    				<div id='<?php echo $resultItens['id'] ?>' style="cursor: pointer" class="cards p-2 m-2 item">
                        				<div class="row">
                            				<div class="col-5">
                                				<img <?php echo "src='data:" . $resultItens['imagemTipo'] . ";base64," . base64_encode($resultItens['imagem']) . "'"; ?> height="135" width="130" class="rounded-lg">
                            				</div>
                            				<div class="col">
                                				<p class="lead mt-4 text-center"><?php echo $resultItens['nome'] ?></p>
												<p class="txt20 text-center mt-3 d-inline"><?php	if($resultItens['intuito'] == "doacao"){
                                                                            							echo "(Doação)";
                                                                        							}
                                                                        							else{
                                                                            							echo "(Troca)";
                                                                        							} ?></p>
                								<p class="txt20 txt-primary text-center ml-4 d-inline"><?php echo $resultItens['categoria'] ?></p>
                            				</div>
                        				</div>
									</div>
<?php               			}
                			} ?>
						</form>
					</div>
						<!-- /CARD -->

						<!-- Paginação -->
                        
						<br><nav id="paginacao" <?php if(!empty(mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa)))){ 
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
<?php							for($pagina = 2; $pagina <= 5; $pagina++){
									if($pagina <= ceil($resultNumPag['COUNT(*)'] / 20)){ ?>
										<li class='page-item paginas' style='cursor:pointer;'><a class='page-link'><?php echo $pagina; ?></a></li>
<?php								}
		                        } ?>
								<li class='page-item'>
									<a class='page-link mudaPagina' id='proxima' style='cursor:pointer;'>
									<span aria-hidden='true'>&raquo;</span><span class='sr-only'>Next</span>
									</a>
								</li>
							</ul>
						</nav>
						<!-- /Paginação -->

					</div>
					
				</div>
				<!-- /estoque -->



			</div>

			


	</div>

<script type="text/javascript">
	$(function(){

		var itemPost = new FormData();
		itemPost.append("usuario",$("#idOutroUsuario").val());
		itemPost.append("operacao","read");
		itemPost.append("tipoPesquisa","usuario");
		var numMaxPag = parseInt($("#idPaginas").val(),10);
		var pesquisaFeita = true;
		var indicePesquisa = 1;
		var minPag = 0;
        var maxPag = 0;

		function pesquisaItem(){
            pesquisaFeita = false;
            itemPost.append("indicePesquisa",parseInt(indicePesquisa,10));
            $.ajax({
                method: 'POST',
                url: 'rudItem.php',
                data: itemPost,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (jason){
                    $(document).find("#itensPesquisa").remove();
                    $(document).find("#resultadoPesquisa").append("<form id='itensPesquisa' action='transacao.php' method='POST' style='height:480px; width:500px; overflow-y: auto'><input type='hidden' id='itemId' name='Itsid'>");
                    for(contItem = 0; jason[contItem]; contItem++){
                        $(document).find("#itensPesquisa").append("<div class='cards p-2 m-2 item' style='cursor: pointer' id='"+jason[contItem].id+"'><div class='row'><div class='col-5'><img src='data:"+jason[contItem].imagemTipo+";base64,"+jason[contItem].imagem+"' height='135' width='130' class='rounded-lg'></div><div class='col'><p class='lead mt-4 text-center'>"+jason[contItem].nome+"</p><p id='displayIntuito"+contItem+"' class='txt20 text-center mt-3 d-inline'></p><p class='txt20 txt-primary text-center ml-4 d-inline'>"+jason[contItem].categoria+"</p></div></div></div>");
                        if(jason[contItem].intuito == "troca"){
                            $(document).find("#displayIntuito"+contItem).html("(Troca)");
                        }
                        else{
                            $(document).find("#displayIntuito"+contItem).html("(Doação)");
                        }
                        $(document).find("#resultadoPesquisa").append("</form>");
                    }

                    $(document).find("#numeracaoPaginas").remove();
                    $(document).find("#paginacao").append("<ul id='numeracaoPaginas' class='pagination justify-content-center'><li class='page-item mudaPagina' id='anterior'><a class='page-link' style='cursor:pointer;'><span aria-hidden='true'>&laquo;</span><span class='sr-only'>Previous</span></a></li>");
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
                                $(document).find("#numeracaoPaginas").append("<li class='page-item active paginas' style='cursor:pointer;'><a class='page-link'>"+pagina+"</a></li>");
                            }
                            else{
                                $(document).find("#numeracaoPaginas").append("<li class='page-item paginas' style='cursor:pointer;'><a class='page-link'>"+pagina+"</a></li>");
                            }
                        }
                    }
                    $(document).find("#numeracaoPaginas").append("<li class='page-item'><a class='page-link mudaPagina' id='proxima' style='cursor:pointer;'><span aria-hidden='true'>&raquo;</span><span class='sr-only'>Next</span></a></li>");
                    $(document).find("#paginacao").append("</ul>");

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

		$(document).on('click','.item',function(){
			$("#itemId").val($(this).attr("id"));
			$("#itensPesquisa").submit();
		})
	})
</script>
</body>
</html>