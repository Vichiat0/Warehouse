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

	//Verifica se a pessoa veio do perfil de alguém ou pelo chat, se não envia de volta para a main
	if($_SESSION['pagina'] != "outraPessoa" && $_SESSION['pagina'] != "perfil" && $_SESSION['pagina'] != "transacao"){
		header("Location: main.php");
        die();
	}

	$_SESSION['pagina'] = "transacao";

	//Recebe dados do usuário
	$queryPesquisa = "SELECT * FROM usuario WHERE email = '$usuario' LIMIT 1";
	$resultUsuario = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
    if(empty($resultUsuario)){ 
		$_SESSION['erroHome'] = "Não foi possível acessar os dados do usuário.<br>
								 Código de erro: " . mysqli_errno($conn);
        header("Location: home.php");
        die();
	}

	//Recebe dados do item primário
	if(isset($_SESSION['idTransacao'])){
		$transacao = $_SESSION['idTransacao']; 
		unset($_SESSION['idTransacao']);
	}
	else{
		$transacao = $_POST['Itsid'];;
	}
	$queryPesquisa = "SELECT * FROM item WHERE id = $transacao LIMIT 1";
    $resultItem = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
    if(empty($resultItem)){ 
		$_SESSION['erroMain'] = "Não foi possível acessar os dados do item.<br>
								 Código de erro: " . mysqli_errno($conn);
        header("Location: main.php");
        die();
	}

	//Verifica se há notificações
	$queryPesquisa = "SELECT tipo FROM notificacao WHERE id=$transacao AND cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='estagio'";
	$resultNotificacao = mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa));
	if(!empty($resultNotificacao)){
		$tipoNotificacao = $resultNotificacao['tipo'];
		$queryDelete = "DELETE FROM notificacao WHERE id=$transacao AND cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='$tipoNotificacao'";
		mysqli_query($conn,$queryDelete);
	}
	else{
		$tipoNotificacao = "";
	}

	//Verifica se está tendo uma transação ou essa é uma nova e estabelece a pessoa1, pessoa2 e o estágio da transação
	$queryPesquisa = "SELECT * FROM transacao WHERE itemPrimario=$transacao LIMIT 1";
	$resultTransacao = mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa));
	if(!empty($resultTransacao)){

		//Recebe o estágio da transação
		$estagioTransacao = $resultTransacao['estagio'];


		//Estabelece quem é o usuário
		if($resultTransacao['cpfcnpj1'] == $resultUsuario['cpfcnpj']){
			$pessoa1 = $resultUsuario;
			$resultOutraPessoa = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM usuario WHERE cpfcnpj='" . $resultTransacao['cpfcnpj2'] . "'"));
			$pessoa2 = $resultOutraPessoa;
			$confirmUsuario = $resultTransacao['confirm1'];
			$confirmOutroUsuario = $resultTransacao['confirm2'];
			$pessoaCpfcnpj = "cpfcnpj1";
		}
		else{
			$resultOutraPessoa = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM usuario WHERE cpfcnpj='" . $resultItem['cpfcnpj'] . "'"));
			$pessoa1 = $resultOutraPessoa;
			$pessoa2 = $resultUsuario;
			$confirmUsuario = $resultTransacao['confirm2'];
			$confirmOutroUsuario = $resultTransacao['confirm1'];
			$pessoaCpfcnpj = "cpfcnpj2";
		}

	}
	else{
		//Valores padrões
		$estagioTransacao = "Contatar";
		$confirmUsuario = "n";
		$confirmOutroUsuario = "n";
		$resultOutraPessoa = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM usuario WHERE cpfcnpj='" . $resultItem['cpfcnpj'] . "'"));
		$pessoa1 = $resultOutraPessoa;
		$pessoa2 = $resultUsuario;
		$pessoaCpfcnpj = "cpfcnpj2";
	}

	//Recebe o endereço de ambos
	$resultCep1 = simplexml_load_file("http://viacep.com.br/ws/" . $resultOutraPessoa['cep'] . "/xml/");
	$resultCep2 = simplexml_load_file("http://viacep.com.br/ws/" . $resultUsuario['cep'] . "/xml/");
	$resultNumero1 = $resultOutraPessoa['numero'];
	$resultNumero2 = $resultUsuario['numero'];

?>
<!DOCTYPE html>
<html>
<head>
	<title>Confirme a transação - Warehouse</title>

	<!-- responsividade  -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta charset="utf-8">

    <!-- JQuery -->
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>

	<!-- Folhas de estilo -->
    <link rel="stylesheet" type="text/css" href="style/style-transacao.css">
    <link rel="stylesheet" type="text/css" href="style/style-warehouse.css">
    <link rel="stylesheet" type="text/css" href="style/bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="js/bootstrap/bootstrap.js"></script>
    
</head>
<body>

<?php 	if($estagioTransacao != "Contatar"){ ?>

			<!-- Modal de transportador -->
			<div class="modal fade" id="transportadorModal" tabindex="-1" aria-labelledby="transportadorLabel" aria-hidden="true">
  				<div class="modal-dialog modal-dialog-centered">
    				<div class="modal-content">
      					<div class="modal-header">
        					<h5 class="modal-title" id="transportadorLabel">Transportador</h5>
        					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          						<span aria-hidden="true">&times;</span>
        					</button>
      					</div>
      					<div class="modal-body">

                    			<!-- Procura transportador -->
				  				<div id="faseum" class='<?php if($resultTransacao['transportador'] == "") { echo "form-group"; } else { echo "d-none"; }?>'>
				    				<label for="inputDate">Selecione a Data e horário de entrega</label>
				    				<input type="datetime-local" class="form-control" id="inputDate" aria-describedby="erroData">
									<small id="erroData" class="invalid-feedback">Data inválida.</small>  
								</div>
                    			<!-- /Procura transportador -->
                    
                    			<!-- Carregando -->
                     			<center><div id="fasedois" class="d-none" role="status">
                            		<span class="sr-only">Loading...</span>
                        		</div></center>
                    			<!-- /Carregando -->

                    			<!-- Resultado -->
                    			<div id="fasetres" <?php if($resultTransacao['transportador'] == null) { echo "class='d-none'"; }?>>

<?php 							if($resultTransacao['transportador'] != null){
									$queryPesquisa = "SELECT * FROM usuario WHERE cpfcnpj='" . $resultTransacao['transportador'] . "'";
									$resultTransportador = mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa)); 
								}?>
                        			<div class="row mb-4 d-flex justify-content-center">
                            			<img id="fotoTransportador" <?php	if($resultTransacao['transportador'] != null){
																				if($resultTransportador['fotoTipo'] == "site"){ 
																					echo "src='style/media/usericon" . bindec($resultTransportador['fotoPerfil']) . ".png'";
					 															}
					 															else{
																					echo "src='data:" . $resultTransportador['fotoTipo'] . ";base64," . base64_encode($resultTransportador['fotoPerfil']) . "'";
																				 }
																			} ?> height="60" width="60" class="rounded-circle">
                        			</div>


                        			<div class="row">
                            			<div class="col text-right">
                                			<p class="lead">Nome</p>
                                			<p class="lead">Modelo do Carro</p>
                                			<p class="lead">Placa do Carro</p>
                                			<p class="lead">Cor do Carro</p>
                            			</div>
                            			<div class="col">
                                			<p id="nomeTransportador" class="lead"><?php 	if($resultTransacao['transportador'] != null){
																								echo $resultTransportador['nome']; } ?></p>
                                			<p id="modeloTransportador" class="lead"><?php 	if($resultTransacao['transportador'] != null){
																								echo $resultTransportador['modeloCarro']; } ?></p>
                                			<p id="placaTransportador" class="lead"><?php 	if($resultTransacao['transportador'] != null){
																								echo $resultTransportador['placaCarro']; } ?></p>
                                			<p id="corTransportador" class="lead rounded-lg"<?php	if($resultTransacao['transportador'] != null){
																										echo "style='background-color:" . $resultTransportador['corCarro'] . ";color:" . $resultTransportador['corCarro'] . ";'"; } ?>>.</p>
                            			</div>
									</div>
                    			</div>
                    			<!-- fase dois -->

      					</div>
      					<div class="modal-footer justify-content-center">

                			<!-- aparecer apenas na fase dois -->
        					<button type="button" id="dispensarTransportador" <?php if($resultTransacao['transportador'] == null) { 
																						echo "class='d-none'"; 
																					} 
																					else{
																						echo "class='btn btn-primary'";
																					}
																					if($confirmUsuario == 's' || 
																						$confirmOutroUsuario == 's' || 
																						$estagioTransacao == "Concluir"){ 
																						echo " disabled"; 
																					}?>>Dispensar</button>

                			<!-- aparecer na fase um -->
                			<button type="button" id="procurarTransportador" <?php if($resultTransacao['transportador'] != null) { 
																						echo "class='d-none'"; 
																					} 
																					else{
																						echo "class='btn btn-primary'";
																					}
																					if($confirmUsuario == 's' || 
																						$confirmOutroUsuario == 's' || 
																						$estagioTransacao == "Concluir"){ 
																						echo " disabled"; 
																					}?>>Procurar</button>

      					</div>
    				</div>
  				</div>
			</div>
			<!-- /Modal de transportador -->
			
<?php 		$resultNotificacao = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='cancelaTransporte'"));
			if(!empty(mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='cancelaTransporte'")))){
				
				$cancelaTransporte = "s";
				$resultQuery = mysqli_query($conn,"SELECT * FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='cancelaTransporte'");
				$resultRemetente = mysqli_fetch_assoc(mysqli_query($conn,"SELECT nome FROM usuario WHERE cpfcnpj='" . $resultNotificacao['remetente'] . "'"))?>
					
				<!-- Modal de notificação de cancelamento do transportador-->
				<div class="modal fade" id='cancelaTransporteModal' tabindex="-1" role="dialog" aria-labelledby="cancelaTransporteModalTitulo" aria-hidden="true">
        			<div class="modal-dialog modal-dialog-centered" role="document">
            			<div class="modal-content bg-danger text-white">
                			<div class="modal-header">
								<h5 class="modal-title" id="cancelaTransporteModalTitulo">Transporte cancelado!</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          							<span aria-hidden="true">&times;</span>
        						</button>
							</div>
							<div id="mensagemCancelaTransporte" class="modal-body">
								<b> <?php echo $resultRemetente['nome']; ?> </b> cancelou o transporte
							</div>
						</div>
					</div>
				</div>
				<!-- /Modal de cancelamento do transportador-->
<?php 			mysqli_query($conn,"DELETE FROM notificacao WHERE (cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' OR cpfcnpj='" . $resultOutraPessoa['cpfcnpj'] . "') AND tipo='cancelaTransporte'");
				$queryUpdate = "UPDATE transacao SET transportador=null, dataEntrega=null WHERE itemPrimario=" . $resultTransacao['itemPrimario'];
                mysqli_query($conn,$queryUpdate);
			} 
			else{
				$cancelaTransporte = "n";
			} 
		} 

		/* Modal de cancelar a transação */
	 	if($estagioTransacao == "Confirmar" || ($estagioTransacao == "Contatar" && $pessoa1['cpfcnpj'] == $resultUsuario['cpfcnpj'])){ ?>
			<div class="modal fade" id="cancelaTransacaoModal" tabindex="-1" role="dialog" aria-labelledby="cancelaTransacaoModalTitulo" aria-hidden="true">
        		<div class="modal-dialog modal-dialog-centered" role="document">
            		<div class="modal-content">
                		<div class="modal-header">
                    		<h5 class="modal-title" id="cancelaTransacaoModalTitulo">Deseja mesmo cancelar a transação?<br> (Você não perderá seu item)</h5>
                		</div>
                		<div class="modal-footer">
                    		<button type="submit" form="transacao" class="btn btn-danger">Sim</button>
                    		<button type="button" class="btn btn-danger" data-dismiss="modal">Não</button>
                		</div>
            		</div>
        		</div>
			</div>
<?php	} ?>
		<!-- /Modal de cancelar a transação -->

		<!-- Modal de seleção de item -->
<?php 	if($resultUsuario['cpfcnpj'] == $pessoa2['cpfcnpj']  && $estagioTransacao != "Concluir"){ ?>
 			<div class="modal fade" id="selecionaItemModal" tabindex="-1" role="dialog" aria-labelledby="erroModalTitulo" aria-hidden="true">
        		<div class="modal-dialog modal-dialog-centered" role="document">
            		<div class="modal-content">
                		<div class="modal-header">
							<h5 class="modal-title" id="erroModalTitulo"><b>Selecione seu item</b></h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          						<span aria-hidden="true">&times;</span>
        					</button>
                		</div>
                		<div class="modal-body">
                    		<!-- CARD -->
							<div id="resultadoPesquisa">
								<div id="itensPesquisa" style="height:480px; width:480px; overflow-y: auto">
								</div>
							</div>
							<!-- /CARD -->

							<!-- Paginação -->
							<br><nav id="paginacao" <?php 	if(!empty(mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa)))){ 
                                								echo "class='m-3'"; 
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
<?php								//Recebe o número de páginas do estoque do usuário
									$queryPesquisa = "SELECT COUNT(*) FROM item WHERE cpfcnpj = '" . $resultUsuario['cpfcnpj'] . "'";
									$resultNumPag = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));

									//Faz a paginação
									for($pagina = 2; $pagina <= 5; $pagina++){
										if($pagina <= ceil($resultNumPag['COUNT(*)'] / 20)){ ?>
											<li class='page-item paginas' style='cursor:pointer;'><a class='page-link'><?php echo $pagina; ?></a></li>
<?php									}
		                    		} ?>
									<li class='page-item'>
										<a class='page-link mudaPagina' id='proxima' style='cursor:pointer;'>
											<span aria-hidden='true'>&raquo;</span><span class='sr-only'>Next</span>
										</a>
									</li>
								</ul>
							</nav>
							<!-- /Paginação -->

							<input type="hidden" id='idUsuario' value='<?php echo $resultUsuario['email']; ?>'>
							<input type="hidden" id='idPaginas' value='<?php echo ceil($resultNumPag['COUNT(*)']/20); ?>'>
                		</div>
            		</div>
        		</div>
			</div>
<?php	} ?>
		<!-- /Modal de seleção de item -->

<!-- Cabeçalho -->
<div class="row px-4 pt-3 bg-light" id="header">

    <!-- botão de voltar -->
	<div class="col-1">
		<a href="perfil.php" style="cursor: pointer">
    	<span>
        	<svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="gray" xmlns="http://www.w3.org/2000/svg">
            	<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
        	</svg>
    	</span>
    	</a>
    </div>        

    <div class="col">
        <p class="text-secondary text-center" id="titleHeader">Confirmar Transação</p>
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

<input type="hidden" id="pessoaCpfcnpj" value="<?php echo $pessoaCpfcnpj; ?>">
<div class="row m-4">

	<div class="col m-2">

		<div class="row">
			<iframe <?php echo "src='http://maps.google.com/maps?q=" . $resultCep1->logradouro . "+" .  $resultNumero1 . "+" . $resultCep1->bairro . "+" . $resultCep1->localidade . "+" . $resultCep1->uf . "&output=embed'" ?> class="w-100 rounded-lg shadow-lg" style="height: 370px;" lang="pt-br" frameborder="0" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
		</div>

		<div class="m-5">

			<!-- endereço de saída -->
			<p class="lead text-center">

				<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="red" xmlns="http://www.w3.org/2000/svg">
  					<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
				</svg>
				<?php 	echo $resultCep1->logradouro . ", " . $resultNumero1 . ", ";
						if($resultCep1->complemento != ""){
							echo $resultCep1->complemento;
						}
						echo " - " .  $resultCep1->bairro . ", " . $resultCep1->localidade . " - " . $resultCep1->uf;
				?><br>

			</p>

			<!-- endereço de entrada -->
			<p class="lead text-center">

				<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-right" fill="green" xmlns="http://www.w3.org/2000/svg">
  					<path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
				</svg>
				<?php 	echo $resultCep2->logradouro . ", " . $resultNumero2;
						if($resultCep2->complemento != ""){
							echo  ", " . $resultCep2->complemento;
						}
						echo " - " .  $resultCep2->bairro . ", " . $resultCep2->localidade . " - " . $resultCep2->uf;
				?>

			</p>


		</div>

<?php 	if($estagioTransacao != "Contatar"){ ?>
			<div class="row justify-content-center d-flex">
				<button type="button" id="btnTransportador" class="btn btn-light shadow mx-3">
					<img src="style/media/carpool.png" class="p-1" height="60" width="55"><br>
					Habilitar/Desabilitar transportador
				</button>
			</div>
<?php	} ?>
	</div>


	<div class="col m-2">

		<!-- CARD -->
        <div class="cards shadow-lg rounded-lg bg-light p-2">
            <div class="row">
                <div class="col p-4">

                	<center>

                	<form id="formPessoa1" class="mb-2 mt-3" method="POST" action='<?php 	if($pessoa1['cpfcnpj'] ==  $resultUsuario['cpfcnpj']){ 
																								echo "perfil.php"; 
																							}
																							else{
																								echo "pessoa.php";
																							} ?>'>
                		<img id="perfilPessoa1" <?php	if($pessoa1['fotoTipo'] == "site"){ 
															echo "src='style/media/usericon" . bindec($pessoa1['fotoPerfil']) . ".png'";
					 									}
					 									else{
															echo "src='data:" . $pessoa1['fotoTipo'] . ";base64," . base64_encode($pessoa1['fotoPerfil']) . "'";
														 } ?> height="50" width="50" class="rounded-circle" style="cursor: pointer;">
						<input type="hidden" name="Itsid" value='<?php echo $transacao ?>'>                		
						<p class="lead d-inline"><?php echo $pessoa1['nome']; ?></p>
					</form>

                	<p class="txt24 text-center"><?php echo $resultItem['nome'] ?></p>
                	<p class="txt20 text-center d-inline"><?php	if($resultItem['intuito'] == "doacao"){
                                                                            					echo "(Doação)";
                                                                        					}
                                                                        					else{
                                                                            					echo "(Troca)";
                                                                        					} ?></p>
                	<p class="txt20 txt-primary text-center ml-4 d-inline"><?php echo $resultItem['categoria'] ?></p>

                	</center>

                </div>

                <div class="col">
                    <img <?php echo "src='data:" . $resultItem['imagemTipo'] . ";base64," . base64_encode($resultItem['imagem']) . "'"; ?> style="height: 220px; width: 220px;" class="rounded-lg">
                </div>
			</div>
			<div class="row">
				<div class="col">
					<label for="itemInfo">Informações</label><br>
					<div class="w-100 pb-4 border" style="height:102px; overflow-y:auto;" id="itemInfo"><?php echo $resultItem['info'] ?></div>
				</div>
			</div>
        </div>
		<!-- /CARD -->

		<!-- Botão de concluir estágio da transação -->																					
		<form id="transacao" method="POST" action="Validacao.php" class="justify-content-center d-flex">
			<button type="submit" id="finalizaEstagio" class="btn btn-primary m-3 w-50 py-3 rounded-lg" <?php if(($resultItem['intuito'] == "troca" && $pessoa2['cpfcnpj'] == $resultUsuario['cpfcnpj'] && $estagioTransacao == "Contatar") ||
																													$confirmUsuario == 's' || 
																													($resultItem['intuito'] == "doacao" && 
																														$estagioTransacao == "Contatar" && 
																														$resultUsuario['cpfcnpj'] == $pessoa2['cpfcnpj'] && 
																														$pessoa2['tickets'] == 0)
																													){ echo "disabled"; }?>>
<?php 			if($estagioTransacao == "Contatar" && $resultUsuario['cpfcnpj'] == $pessoa1['cpfcnpj']){ 
					echo "Aceitar transação"; 
				}else{ 
					echo $estagioTransacao; 
				} 
				if($resultItem['intuito'] == "doacao" && $estagioTransacao == "Contatar" && $pessoa1['cpfcnpj'] != $resultUsuario['cpfcnpj']) { 
					echo "<br>" . intval($pessoa2['tickets']) . "x tickets disponíveis"; 
				}
				if($tipoNotificacao != ""){ ?>
					<sup><span class='badge badge-pill badge-danger'>!</span></sup>
<?php			}?>
			</button>
			<input type="hidden" id='estagio' name="Itsestagio" value='<?php echo $estagioTransacao ?>'>
			<input type="hidden" name="Itstipo" value='<?php echo $resultItem['intuito'] ?>'>
			<input type="hidden" id="idTransacao" name="Itsitemprimario" value='<?php echo $resultItem['id'] ?>'>
<?php 		switch($estagioTransacao){ 
				case "Contatar":
					if($resultItem['intuito'] == "troca"){ ?>
						<input type="hidden" name="Itsitemsecundario" id='itemId' <?php if($pessoa1['cpfcnpj'] == $resultUsuario['cpfcnpj']) { echo "value='" . $resultTransacao['itemSecundario'] . "'"; }?>>
<?php				}
					if(($resultUsuario['cpfcnpj'] == $pessoa1['cpfcnpj']) || ($confirmOutroUsuario == "n" && $confirmUsuario == "s")){ ?>
						<button type="button" id="cancelaTransacao" class="btn btn-primary m-3 w-50 py-3 rounded-lg"><?php if($confirmOutroUsuario == "n" && $confirmUsuario == "s") { echo "Cancelar transação"; } else{ echo "Recusar transação"; } ?></button>
						<input type="hidden" id="confirmCancela" name="Itscancelar" value='n'>
<?php				}
				break;
				case "Confirmar": 
					if($resultItem['intuito'] == "troca"){ ?>
						<input type="hidden" name="Itsitemsecundario" id='itemId' value='<?php echo $resultTransacao['itemSecundario']; ?>'>
<?php 				} ?>
					<input type="hidden" id="confirmCancela" name="Itscancelar" value='n'>
					<input type="hidden" name="Itstransportador" id='transportadorId' value=''>
					<button type="button" id="cancelaTransacao" class="btn btn-primary m-3 w-50 py-3 rounded-lg">Cancelar transação</button>
<?php			break;
			} ?>
		</form>
		<!-- /Botão de concluir estágio da transação -->

		<!-- Mensagem de espera do outro usuário -->
<?php 	if($confirmOutroUsuario == "n" && $confirmUsuario == "s"){ ?>
			<br><center>Aguardando resposta do outro usuário.</center><br>
<?php	} ?>
		<!-- /Mensagem de espera do outro usuário -->

		<!-- Botão de adicionar item para troca -->
<?php   if($resultItem['intuito'] == "troca"){ ?>
			Item para troca:
<?php	} ?>
<?php 	if($estagioTransacao != "Contatar"){ ?>
			<form id="formPessoa2" method="POST" action='<?php 	if($pessoa2['cpfcnpj'] ==  $resultUsuario['cpfcnpj']){ 
																	echo "perfil.php"; 
																}
																else{
																	echo "pessoa.php";
																} ?>'>
				<center><?php 	if($resultItem['intuito'] == "troca"){ 
									echo "Proprietário(a): "; 
								}else{
									echo "Donatário(a): ";
								}
								echo $pessoa2['nome']; ?>
				<img id="perfilPessoa2" <?php	if($pessoa2['fotoTipo'] == "site"){ 
													echo "src='style/media/usericon" . bindec($pessoa2['fotoPerfil']) . ".png'";
					 							}
					 							else{
													echo "src='data:" . $pessoa1['fotoTipo'] . ";base64," . base64_encode($pessoa2['fotoPerfil']) . "'";
					 							} ?> height="50" width="50" class="rounded-circle" style="cursor: pointer;"><center>
				<input type="hidden" name="Itsid" <?php if($resultItem['intuito'] == "troca") { echo"value='" . $resultTransacao['itemSecundario'] . "'"; } ?>>
			</form>
<?php	} ?>

<?php   if($resultItem['intuito'] == "troca"){ ?>
			<div>
				<button id="selecionaItem" class="btn btn-light py-4 w-100 mt-2 shadow" type="button" <?php if($pessoa1['cpfcnpj'] == $resultUsuario['cpfcnpj'] || $confirmUsuario == 's' || $confirmOutroUsuario == 's' || $estagioTransacao == "Concluir"){ echo "disabled"; } ?>>
<?php 				if($estagioTransacao == "Contatar" && $pessoa2['cpfcnpj'] == $resultUsuario['cpfcnpj'] && $confirmUsuario == "n"){ ?>
						<div id="selecao">
							Escolher item para trocar<br>
							<svg width="3em" height="3em" viewBox="0 0 16 16" class="bi bi-plus-square-fill" fill="gray" xmlns="http://www.w3.org/2000/svg">
  								<path fill-rule="evenodd" d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm6.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
							</svg>
						</div>
<?php				}
					else{ 
						$queryPesquisa = "SELECT * FROM item WHERE id=" . $resultTransacao['itemSecundario']; 
						$resultItem = mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa)); ?>
						<div id='selecao' class='cards rounded-lg bg-dark text-light p-2 m-2'>
							<input type='hidden' id='itemSelect' value='<?php echo $resultItem['id']; ?>'>
							<div class='row'>
								<div class='col-5'>
									<img src='data:<?php echo $resultItem['imagemTipo'] . ";base64," . base64_encode($resultItem['imagem']); ?>' height='110' width='105' class='rounded-lg'>
								</div>
								<div class='col'>
									<p class='lead mt-4 text-center'><?php echo $resultItem['nome']; ?></p>
									<p class='txt20 text-center mt-3 d-inline'><?php	if($resultItem['intuito'] == "doacao"){
                                                                            				echo "(Doação)";
                                                                        				}
                                                                        				else{
                                                                            				echo "(Troca)";
                                                                        				} ?></p>
									<p class='txt20 txt-primary text-center ml-4 d-inline'><?php echo $resultItem['categoria']; ?></p>
								</div>
							</div>
						</div>
<?php				} ?>
				</button>
			</div>
<?php	} ?>
		<!-- /Botão de adicionar item para troca -->

	</div>
	
</div>


	<script type='text/javascript'>
		$(function(){

			var itemPost = new FormData();
			itemPost.append("usuario",$("#idUsuario").val());
			itemPost.append("tipoPesquisa","usuario");
			itemPost.append("tipoUpdate","uso");
			itemPost.append("itemAtual","");

			var transportadorPost = new FormData();
			transportadorPost.append("transacao",$("#idTransacao").val());
			transportadorPost.append("quemDeletou","negociadores");
			transportadorPost.append("pessoaCpfcnpj",$("#pessoaCpfcnpj").val());

			var numMaxPag = parseInt($("#idPaginas").val(),10);
			var pesquisaFeita = true;
			var indicePesquisa = 1;
			var minPag = 0;
        	var maxPag = 0;

<?php		if($estagioTransacao != "Contatar"){
				if($cancelaTransporte == "s"){ ?>
					$("#fasetres").attr("class","d-none");
					$("#faseum").attr("class","form-group");
					$("#dispensarTransportador").attr("class","d-none");
					$("#procurarTransportador").attr("class","btn btn-primary");
					$("#cancelaTransporteModal").modal();
<?php			}
			} ?>

			function pesquisaItem(){
				itemPost.append("operacao","read");
            	pesquisaFeita = false;
				var dia;
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
                    	$(document).find("#resultadoPesquisa").append("<div id='itensPesquisa' style='height:480px; width:475px; overflow-y: auto'>");
                    	for(contItem = 0; jason[contItem]; contItem++){
                        	$(document).find("#itensPesquisa").append("<div class='cards p-2 m-2 item rounded-lg text-light bg-dark' id='"+jason[contItem].id+"'style='cursor: pointer'>");
							$(document).find("#"+jason[contItem].id).append("<div class='row'><div class='col-5'><img id='idImg"+jason[contItem].id+"' src='data:"+jason[contItem].imagemTipo+";base64,"+jason[contItem].imagem+"' height='135' width='130' class='rounded-lg'></div><div class='col'><p id='idNome"+jason[contItem].id+"' class='lead mt-4 text-center'>"+jason[contItem].nome+"</p><p id='displayIntuito"+jason[contItem].id+"' class='txt20 text-center mt-3 d-inline'></p><p id='idCategoria"+jason[contItem].id+"' class='txt20 txt-primary text-center ml-4 d-inline'>"+jason[contItem].categoria+"</p></div></div></div>");
                        	if(jason[contItem].intuito == "troca"){
                            	$(document).find("#displayIntuito"+jason[contItem].id).html("(Troca)");
                        	}
                        	else{
                            	$(document).find("#displayIntuito"+jason[contItem].id).html("(Doação)");
                        	}
                        	$(document).find("#resultadoPesquisa").append("</div>");
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

			$("#btnTransportador").click(function(){
				$("#transportadorModal").modal();
			});

			$("#procurarTransportador").click(function(){
				var dataHorarioTransportador = new Date($("#inputDate").val());
				if(dataHorarioTransportador.toDateString() != "Invalid Date"){
					transportadorPost.append("operacao","create");
					$("#faseum").attr("class","d-none");
					$("#procurarTransportador").attr("class","d-none");
					$("#fasedois").attr("class","form-group spinner-border text-secondary");
					$("#inputDate").attr("class","form-control is-valid");
					if(dataHorarioTransportador.getDate() < 10){
						dia = "0"+dataHorarioTransportador.getDate();
					}
					else{
						dia = dataHorarioTransportador.getDate();
					}
					transportadorPost.append("dataTransporte",dataHorarioTransportador.getFullYear()+"-"+(dataHorarioTransportador.getMonth()+1)+"-"+dia);
					transportadorPost.append("horarioTransporte",dataHorarioTransportador.getHours()+":"+dataHorarioTransportador.getMinutes());
					transportadorPost.append("dataCompleta",$("#inputDate").val());
					$.ajax({
                		method: 'POST',
                		url: 'createDeleteTransportador.php',
                		data: transportadorPost,
                		contentType: false,
                		processData: false,
                		dataType: 'json',
                		success: function (jason){
							if(jason.nome == null){
								window.setTimeout(function(){
									$("#fasedois").attr("class","d-none");
									$("#faseum").attr("class","form-group");
									$("#procurarTransportador").attr("class","btn btn-primary");
								},1500);
								$("#inputDate").attr("class","form-control is-invalid");
								$("#erroData").html("Sinto muito, não foi encontrado transportador nesse horário.");
							}
							else{
								window.setTimeout(function(){
									$("#fasedois").attr("class","d-none");
									$("#fasetres").attr("class","form-group");
									$("#fotoTransportador").attr("src",jason.fotoPerfil);
									$("#nomeTransportador").html(jason.nome);
									$("#modeloTransportador").html(jason.modeloCarro);
									$("#placaTransportador").html(jason.placaCarro);
									$("#corTransportador").css("background-color",jason.corCarro);
									$("#corTransportador").css("color",jason.corCarro);
									$("#dispensarTransportador").attr("class","btn btn-primary");
								},1500);
							}
						},
						error: function(erro){
							alert(erro.responseText);
						}
					});
				}
				else{
					$("#erroData").html("Data inválida.");
					$("#inputDate").attr("class","form-control is-invalid");
				}
			})

			$("#dispensarTransportador").click(function(){
				$("#fasetres").attr("class","d-none");
				$("#dispensarTransportador").attr("class","d-none");
				$("#fasedois").attr("class","form-group spinner-border text-secondary");
				transportadorPost.append("operacao","delete");
				$.ajax({
                	method: 'POST',
                	url: 'createDeleteTransportador.php',
                	data: transportadorPost,
                	contentType: false,
                	processData: false,
                	success: function (){
						window.setTimeout(function(){
							$("#fasedois").attr("class","d-none");
							$("#faseum").attr("class","form-group");
							$("#procurarTransportador").attr("class","btn btn-primary");
						},1500);
					},
					error: function(erro){
						alert(erro.responseText);
					}
				});
			});

			$("#perfilPessoa1").click(function(){
				$("#formPessoa1").submit();
			});

			$("#perfilPessoa2").click(function(){
				$("#formPessoa2").submit();
			});

			$("#selecionaItem").click(function(){
				pesquisaItem();
				$("#selecionaItemModal").modal();
			});


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

			//Quando seleciona um item
			$(document).on('click','.item',function(){
				if($("#estagio").val() != "Contatar"){
					itemPost.append("itemAtual",$(document).find("#itemSelect").val());
					itemPost.append("itemNovo",$(document).find(this).attr("id"));
					itemPost.append("operacao","update");
					$.ajax({
                		method: 'POST',
                		url: 'rudItem.php',
                		data: itemPost,
                		contentType: false,
                		processData: false,
                		dataType: 'json', 
                		error: function (error){
                    		alert(error.responseText);
                		}      
            		});
				}
				$("#itemId").val($(this).attr("id"));
				$("#finalizaEstagio").prop("disabled",false);
				$(document).find("#selecao").remove();
				$("#selecionaItem").append("<div id='selecao' class='cards rounded-lg bg-dark text-light p-2 m-2'><div class='row'><div class='col-5'><img src='"+$(this).find("#idImg"+$(this).attr("id")).attr("src")+"' height='110' width='105' class='rounded-lg'></div><div class='col'><p class='lead mt-4 text-center'>"+$(this).find("#idNome"+$(this).attr("id")).html()+"</p><p class='txt20 text-center mt-3 d-inline'>"+$(this).find("#displayIntuito"+$(this).attr("id")).html()+"</p><p class='txt20 txt-primary text-center ml-4 d-inline'>"+$(this).find("#idCategoria"+$(this).attr("id")).html()+"</p></div></div></div>");
				$("#selecionaItemModal").modal('hide');
			});

			//Quando clica no botão de cancelar a transação
            $("#cancelaTransacao").click(function() {
                $("#confirmCancela").val("s");
                $('#cancelaTransacaoModal').modal();
            });

            //Quando fecha o modal de apagar a conta
            $('#cancelaTransacaoModal').on('hidden.bs.modal', function (e) {
                $("#confirmCancela").val("n");
            });
		})
	</script>

</body>
</html>