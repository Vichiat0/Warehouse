<!-- Verifica se o usuário está logado e inicia BD-->
<?php
	session_start();
    if(!empty($_SESSION['usuario'])){
        $usuario = $_SESSION['usuario'];
    }
    else{
        header("Location: home.php");
        die();
	}
	$_SESSION['pagina'] = "perfil";
	include_once("Conexao_BD.php");
    $queryPesquisa = "SELECT * FROM usuario WHERE email = '$usuario' LIMIT 1";
    $resultUsuario = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
	if(empty($resultUsuario)){
		$_SESSION['usuario'] = null;
		$_SESSION['erroHome'] = "Não foi possível acessar os dados do perfil, por favor, tente reconectar-se.<br>
								 Código de erro: " . mysqli_errno($conn);
		header("Location: home.php");
		die();
	}
	$queryPesquisa = "SELECT * FROM eventostransporte WHERE pessoaId = '$usuario' LIMIT 1";
	$resultEvento = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));

	//Recupera transações
	$queryPesquisa = "SELECT * FROM transacao WHERE cpfcnpj1='" . $resultUsuario['cpfcnpj'] . "' OR cpfcnpj2='" . $resultUsuario['cpfcnpj'] . "'";
	$resultTransacao = mysqli_fetch_assoc(mysqli_query($conn,$queryPesquisa));
	
	//Recupera notificações
	$numNotificacaoNova = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='nova'"));

?>
<!DOCTYPE html>
<html>
<head>
	<title>Perfil - Warehouse</title>

	<!-- Jquery -->
	<script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>

	<!-- Folhas de estilo -->
	<link rel="stylesheet" type="text/css" href="style/perfil.css">
	<link rel="stylesheet" type="text/css" href="style/style-warehouse.css">
	<link rel="stylesheet" type="text/css" href="style/bootstrap/css/bootstrap.css">
	<script type="text/javascript" src="js/bootstrap/bootstrap.js"></script>

	<meta charset="utf-8">

</head>
<body class="overflow-hidden">

<?php 	if($numNotificacaoNova['COUNT(*)'] > 0){ ?>

			<!-- Modal de transações novas -->
			<div class="modal fade" id="transacaoNovaModal" tabindex="-1" role="dialog" aria-labelledby="transacaoNovaModalTitulo" aria-hidden="true">
        		<div class="modal-dialog modal-dialog-centered" role="document">
            		<div class="modal-content">
                		<div class="modal-header">
                    		<h5 class="modal-title" id="transacaoNovaModalTitulo">Novas transações!</h5>
                		</div>
                		<div class="modal-body">
                    		Você tem <?php echo $numNotificacaoNova['COUNT(*)']; 
												if($numNotificacaoNova['COUNT(*)'] == 1) { 
													echo " transação nova!"; 
												}else{
													echo " transações novas!";
												} ?>
                		</div>
            		</div>
        		</div>
			</div>
			<!-- /Modal de transações novas -->
<?php		mysqli_query($conn,"UPDATE notificacao SET tipo='estagio' WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='nova'");
		}

		if(!empty(mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='recusado'")))){
			$recusaTransacao = "s";
			$idNotifica = 1;
			$resultQuery = mysqli_query($conn,"SELECT * FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='recusado'");
			while ($resultNotificacao = mysqli_fetch_assoc($resultQuery)){
				$resultRemetente = mysqli_fetch_assoc(mysqli_query($conn,"SELECT nome FROM usuario WHERE cpfcnpj='" . $resultNotificacao['remetente'] . "'"))?>
				
				<!-- Modal de notificação de recusa da transação-->
				<div class="modal fade" <?php echo "id='recusaTransacaoModal" . $idNotifica . "'"; ?> tabindex="-1" role="dialog" aria-labelledby="recusaTransacaoModalTitulo" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content bg-danger text-white">
							<div class="modal-header">
								<h5 class="modal-title" id="recusaTransacaoModalTitulo">Pedido de transação recusado!</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									  <span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div id="mensagemRecusaTransacao" class="modal-body">
								<b> <?php echo $resultRemetente['nome']; ?> </b> recusou seu pedido de transação.
							</div>
						</div>
					</div>
				</div>
				<!-- /Modal de notificação de recusa da transação-->

<?php 			$idNotifica++;
			}
			mysqli_query($conn,"DELETE FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='recusado'");
		} 
		else{
			$recusaTransacao = "n";
		}

		if(!empty(mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='cancelamento'")))){
			$cancelaTransacao = "s";
			$idNotifica = 1;
			$resultQuery = mysqli_query($conn,"SELECT * FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='cancelamento'");
			while ($resultNotificacao = mysqli_fetch_assoc($resultQuery)){
				$resultRemetente = mysqli_fetch_assoc(mysqli_query($conn,"SELECT nome FROM usuario WHERE cpfcnpj='" . $resultNotificacao['remetente'] . "'"))?>
				
				<!-- Modal de notificação de cancelamento da transação-->
				<div class="modal fade" <?php echo "id='cancelaTransacaoModal" . $idNotifica . "'"; ?> tabindex="-1" role="dialog" aria-labelledby="cancelaTransacaoModalTitulo" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content bg-danger text-white">
							<div class="modal-header">
								<h5 class="modal-title" id="cancelaTransacaoModalTitulo">Transação cancelada!</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									  <span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div id="mensagemCancelaTransacao" class="modal-body">
								<b> <?php echo $resultRemetente['nome']; ?> </b> cancelou a transação com você.
							</div>
						</div>
					</div>
				</div>
				<!-- /Modal de notificação de cancelamento da transação-->

<?php 			$idNotifica++;
			}
			mysqli_query($conn,"DELETE FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='cancelamento'");
		} 
		else{
			$cancelaTransacao = "n";
		}

		if(!empty(mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='conclusao'")))){
			$concluiTransacao = "s";
			$idNotifica = 1;
			$resultQuery = mysqli_query($conn,"SELECT * FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='conclusao'");
			while ($resultNotificacao = mysqli_fetch_assoc($resultQuery)){
				$resultRemetente = mysqli_fetch_assoc(mysqli_query($conn,"SELECT nome FROM usuario WHERE cpfcnpj='" . $resultNotificacao['remetente'] . "'"))?>
				
				<!-- Modal de notificação de conclusão da transação-->
				<div class="modal fade" <?php echo "id='concluiTransacaoModal" . $idNotifica . "'"; ?> tabindex="-1" role="dialog" aria-labelledby="concluiTransacaoModalTitulo" aria-hidden="true">
					<div class="modal-dialog modal-dialog-centered" role="document">
						<div class="modal-content bg-success text-white">
							<div class="modal-header">
								<h5 class="modal-title" id="concluiTransacaoModalTitulo">Transação concluída!</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
									  <span aria-hidden="true">&times;</span>
								</button>
							</div>
							<div id="mensagemConcluiTransacao" class="modal-body">
								Sua transação com <b> <?php echo $resultRemetente['nome']; ?> </b> foi concluída!
							</div>
						</div>
					</div>
				</div>
				<!-- /Modal de notificação de conclusão da transação-->

<?php 			$idNotifica++;
			}
			mysqli_query($conn,"DELETE FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='conclusao'");
		} 
		else{
			$concluiTransacao = "n";
		}

	 	if($resultUsuario['transportador'] == "S"){ 
			$numNotificacaoTransporte = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='transporte'"));
			if($numNotificacaoTransporte['COUNT(*)'] > 0){ ?>
			 	<!-- Modal de transportes novos -->
				<div class="modal fade" id="transporteNovoModal" tabindex="-1" role="dialog" aria-labelledby="transporteNovoModalTitulo" aria-hidden="true">
        			<div class="modal-dialog modal-dialog-centered" role="document">
            			<div class="modal-content">
                			<div class="modal-header">
								<h5 class="modal-title" id="transporteNovoModalTitulo">Novos transportes!</h5>
								<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          							<span aria-hidden="true">&times;</span>
        						</button>
                			</div>
                			<div class="modal-body">
								Você tem <?php echo $numNotificacaoTransporte['COUNT(*)']; 
												if($numNotificacaoTransporte['COUNT(*)'] == 1) { 
													echo " transporte novo a realizar!"; 
												}else{
													echo " transportes novos a realizar!";
												} ?>
                			</div>
            			</div>
        			</div>
				</div>
				<!-- /Modal de transportes novos -->
<?php			mysqli_query($conn,"DELETE FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='transporte'");
			} ?>

			<!-- Modal de informações da transação para o transportador-->
			<div class="modal fade" id="transporteModal" tabindex="-1" role="dialog" aria-labelledby="transporteModalTitulo" aria-hidden="true">
        		<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            		<div class="modal-content">
                		<div class="modal-header">
							<h5 class="modal-title" id="transporteModalTitulo">Informações sobre o transporte</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          						<span aria-hidden="true">&times;</span>
        					</button>
                		</div>
                		<div class="modal-body">
							<center><img id="foto2" src='style/media/usericon1.png' class="rounded-circle" width="50" height="50"><p id="nome2" class="d-inline">Godofredo</p>
							<div class="row m-4">
								<div class="col m-2">
									<div class="row">
										<iframe id="mapa1" class="w-100 rounded-lg shadow-lg" style="height: 230px;" lang="pt-br" frameborder="0" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
									</div>
								</div>
							</div>
							<img id="foto1" src='style/media/usericon1.png' class="rounded-circle" width="50" height="50"><p id="nome1" class="d-inline">Godofredo</p>
							<div class="row m-4">
								<div class="col m-2">
									<div class="row">
										<iframe id="mapa2" class="w-100 rounded-lg shadow-lg" style="height: 230px;" lang="pt-br" frameborder="0" allowfullscreen="" aria-hidden="false" tabindex="0"></iframe>
									</div>
								</div>
							</div>
							<p class="lead text-center">

								<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="red" xmlns="http://www.w3.org/2000/svg">
  									<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
								</svg> <a id="end1"></a>

							</p>

							<p class="lead text-center">

								<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-right" fill="green" xmlns="http://www.w3.org/2000/svg">
  									<path fill-rule="evenodd" d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8z"/>
								</svg> <a id="end2"></a>

							</p>
							<p id="dataEntrega" class="lead">Data de entrega: 10-11-2020<br>Horário: 12:00</p></center>
						</div>
						<div class="modal-footer justify-content-center">
							<button type="button" id="btnCancelaTransporte" class="btn btn-danger" data-dismiss="modal">Cancelar transporte</button>
						</div>
            		</div>
        		</div>
			</div>	
			<!-- /Modal de informações da transação para o transportador-->

			<!-- Modal de cancelamento do transporte -->
			<div class="modal fade" id="confirmCancelaTransporteModal" tabindex="-1" role="dialog" aria-labelledby="confirmCancelaTransporteModalTitulo" aria-hidden="true">
        		<div class="modal-dialog modal-dialog-centered" role="document">
            		<div class="modal-content">
                		<div class="modal-header">
                    		<h5 class="modal-title" id="confirmCancelaTransporteModalTitulo">Deseja mesmo cancelar esse transporte?<br> (Você não perderá seu item)</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          						<span aria-hidden="true">&times;</span>
        					</button>
						</div>
                		<div class="modal-footer">
                    		<button type="button" id="confirmCancelaTransporte" class="btn btn-danger" data-dismiss="modal">Sim</button>
                    		<button type="button" class="btn btn-danger" data-dismiss="modal">Não</button>
                		</div>
            		</div>
        		</div>
			</div>
			<!-- /Modal de cancelamento do transporte -->

<?php 		if(!empty(mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='cancelaTransporte'")))){
				$cancelaTransporte = "s";
				$idNotifica = 1;
				$resultQuery = mysqli_query($conn,"SELECT * FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='cancelaTransporte'");
				while ($resultNotificacao = mysqli_fetch_assoc($resultQuery)){
					$resultRemetente = mysqli_fetch_assoc(mysqli_query($conn,"SELECT nome FROM usuario WHERE cpfcnpj='" . $resultNotificacao['remetente'] . "'"))?>
					
					<!-- Modal de notificação de cancelamento do transportador-->
					<div class="modal fade" <?php echo "id='cancelaTransporteModal" . $idNotifica . "'"; ?> tabindex="-1" role="dialog" aria-labelledby="cancelaTransporteModalTitulo" aria-hidden="true">
        				<div class="modal-dialog modal-dialog-centered" role="document">
            				<div class="modal-content bg-danger text-white">
                				<div class="modal-header">
									<h5 class="modal-title" id="cancelaTransporteModalTitulo">Transporte cancelado!</h5>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close">
          								<span aria-hidden="true">&times;</span>
        							</button>
								</div>
								<div id="mensagemCancelaTransporte" class="modal-body">
									Seu transporte com <b> <?php echo $resultRemetente['nome']; ?> </b> foi cancelado
								</div>
							</div>
						</div>
					</div>
					<!-- /Modal de cancelamento do transportador-->

<?php 				$idNotifica++;
				}
				mysqli_query($conn,"DELETE FROM notificacao WHERE cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo='cancelaTransporte'");
			} 
			else{
				$cancelaTransporte = "n";
			}?>
			<input type="hidden" id="cancelaTransporte" value="<?php echo $cancelaTransporte; ?>">	
<?php 	} ?>


	<div class="position-absolute ml-4 m-2">
		<a href="main.php">
		<svg width="2.5em" height="2.5em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="white" xmlns="http://www.w3.org/2000/svg">
  			<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
		</svg>
		</a>
	</div>


	<!-- BACKGROUND DA PÁGINA -->
	<img <?php echo"src='style/media/presetbg" . $resultUsuario['fotoBg'] . ".jpg'"; ?> class="bg-dark" id="background">


	<!-- CORPO DA PÁGINA -->
	<div  class="row mt-5 h-80 w-100 center">




		<!-- **CARD ESQUERDO** -->
		<div class="col ml-5 text-light bgdark rounded shadow-lg">
			<div class="ml-3">
				
				<h3 class="text-center py-3">Dados Pessoais</h3>

				<!-- dados pessoais -->
				<p class="m-0">Nome: <?php echo $resultUsuario['nome']; ?></p>
				<p class="m-0">Email: <?php echo " " . $resultUsuario['email'] ?></p>
				<p class="m-0">Descrição: <?php if(empty($resultUsuario['descricao'])){
        											echo " (Sem descrição)";
        										}
                                				else{
                                    				echo $resultUsuario['descricao'];
                                				}
                            				?></p>

				<a href="config.php" class="btn d-block mt-4" id="link">Alterar Informações
					<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-pencil-square" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  						<path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456l-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
  						<path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/>
					</svg>
				</a>



				<!-- Modo transporte -->
<?php 			if($resultUsuario['transportador'] == "S"){ ?>
					<h4 class="text-center pt-5 pb-3">Disponibilidade como transportador nessa semana</h4>
					<p style="overflow-y: scroll;height:230px;" class="m-0">
						<a id="dDom"><b>Dom</b>: Não está disponível<br></a>
						<a id="dSeg"><b>Seg</b>: Não está disponível<br></a>
						<a id="dTer"><b>Ter</b>: Não está disponível<br></a>
						<a id="dQua"><b>Qua</b>: Não está disponível<br></a>
						<a id="dQui"><b>Qui</b>: Não está disponível<br></a>
						<a id="dSex"><b>Sex</b>: Não está disponível<br></a>
						<a id="dSab"><b>Sab</b>: Não está disponível<br></a>
					</p>
<?php 			} ?>

			</div>
		</div>
		<!--  -->






		<!-- **MEIO** -->
		<div class="col px-5">
			<center>
			
			<!-- PERFIL -->
			<div class="">

				<!-- FOTO DE PERFIL -->
				<img <?php	if($resultUsuario['fotoTipo'] == "site"){ 
								echo "src='style/media/usericon" . bindec($resultUsuario['fotoPerfil']) . ".png'";
					 		}
					 		else{
								echo "src='data:" . $resultUsuario['fotoTipo'] . ";base64," . base64_encode($resultUsuario['fotoPerfil']) . "'";
					 		} ?> class="" height="150" width="150">
				
				<!-- NOME -->
				<h3 class="text-light font-weight-light"><?php echo $resultUsuario['nome']; ?></h3>

			</div>

			<div class="row mx-0 my-5">
				<div class="col">
					<img src="style/media/ticket1.png" class="" height="80" width="80">
					<p class="my-3 font-weight-bold">Tickets: <?php echo intval($resultUsuario['tickets'],10); ?></p>
				</div>
			</div>

			<div class="">
				<a href="Validacao.php"><button class="btn btn-danger w-50 p-2" id="logout"> Logout </button></a>
			</div>


			</center>
		</div>
		<!--  -->







		<!-- **CARD DIREITO** -->
		<div class="col mr-3 w-100 h-100 bgdark text-light rounded-lg shadow-lg" style="overflow-y:auto">
			<h3 class="text-center text-light py-3">Transações pendentes</h3>
<?php		$queryPesquisa = "SELECT * FROM transacao WHERE cpfcnpj1='" . $resultUsuario['cpfcnpj'] . "' OR cpfcnpj2='" . $resultUsuario['cpfcnpj'] . "' OR transportador='" . $resultUsuario['cpfcnpj'] . "'";
			if(!empty(mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa)))){ ?>
				<form id="escolhaTransacao" action="transacao.php" method="POST">
					<input type="hidden" id="idEscolhido" name="Itsid">
<?php				$resultQuery = mysqli_query($conn,$queryPesquisa);
					while($resultTransacao = mysqli_fetch_assoc($resultQuery)){
						$resultItemPrimario = mysqli_fetch_assoc(mysqli_query($conn,"SELECT intuito, nome FROM item WHERE id=" . $resultTransacao['itemPrimario']));
						if($resultTransacao['cpfcnpj1'] == $resultUsuario['cpfcnpj']){
							$pesquisaCpfcnpj = "cpfcnpj2";
						}
						else{
							$pesquisaCpfcnpj = "cpfcnpj1";
						}
						$cpfcnpjOutraPessoa = mysqli_fetch_assoc(mysqli_query($conn,"SELECT $pesquisaCpfcnpj FROM transacao WHERE itemPrimario=" . $resultTransacao['itemPrimario']));
						$resultOutraPessoa = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM usuario WHERE cpfcnpj='" . $cpfcnpjOutraPessoa[$pesquisaCpfcnpj] . "'"));
						$resultNotificacao = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM notificacao WHERE id=" . $resultTransacao['itemPrimario'] . " AND cpfcnpj='" . $resultUsuario['cpfcnpj'] . "' AND tipo!='chat'"));
						if($resultTransacao['transportador'] != $resultUsuario['cpfcnpj']){
							if($resultItemPrimario['intuito'] == "troca"){ 
								$resultItemSecundario = mysqli_fetch_assoc(mysqli_query($conn,"SELECT nome FROM item WHERE id=" . $resultTransacao['itemSecundario']));?>
							
								<!-- Card troca -->
								<div class="bg-dark px-2 my-3 rounded-lg transacao" style="cursor: pointer;">
									<input type="hidden" class="ids" value='<?php echo $resultTransacao['itemPrimario']; ?>'>
									<div class="row text-center font-weight-bold py-2">
										<div class="col-5"><?php echo $resultItemPrimario['nome'] ?></div>
										<div class="col">
											<svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-arrow-left-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
 		 										<path fill-rule="evenodd" d="M1 11.5a.5.5 0 0 0 .5.5h11.793l-3.147 3.146a.5.5 0 0 0 .708.708l4-4a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 11H1.5a.5.5 0 0 0-.5.5zm14-7a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H14.5a.5.5 0 0 1 .5.5z"/>
											</svg>
										</div>
										<div class="col-5"><?php echo $resultItemSecundario['nome'] ?></div>
									</div>
									<center><img <?php 	if($resultOutraPessoa['fotoTipo'] == "site"){ 
                                    						echo "src='style/media/usericon" . bindec($resultOutraPessoa['fotoPerfil']) . ".png'";
                                						}
                                						else{
                                    						echo "src='data:" . $resultOutraPessoa['fotoTipo'] . ";base64," . base64_encode($resultOutraPessoa['fotoPerfil']) . "'";
                                						}?> class="rounded-circle" width="50" height="50">
									<p class="d-inline"><?php echo $resultOutraPessoa['nome']; if(!empty($resultNotificacao)){ echo "<sup><span class='badge badge-pill badge-danger'>!</span></sup></p>"; } ?></center>
								</div>
								<!-- /Card troca -->
<?php						}
							else{ ?>

								<!-- Card doação -->
								<div class="bg-dark px-2 my-3 rounded-lg transacao" style="cursor: pointer;">
									<input type="hidden" class="ids" value='<?php echo $resultTransacao['itemPrimario']; ?>'>
									<div class="row text-center font-weight-bold py-2">
										<div class="col">
											<svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-heart-fill" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  												<path fill-rule="evenodd" d="M8 1.314C12.438-3.248 23.534 4.735 8 15-7.534 4.736 3.562-3.248 8 1.314z"/>
											</svg>
										</div>
										<div class="col-9"><?php echo $resultItemPrimario['nome'] ?></div>
										<div class="col"></div>
									</div>
									<center><img <?php 	if($resultOutraPessoa['fotoTipo'] == "site"){ 
                                    						echo "src='style/media/usericon" . bindec($resultOutraPessoa['fotoPerfil']) . ".png'";
                                						}
                                						else{
                                   							echo "src='data:" . $resultOutraPessoa['fotoTipo'] . ";base64," . base64_encode($resultOutraPessoa['fotoPerfil']) . "'";
                             							}?> class="rounded-circle" width="50" height="50">
									<p class="d-inline"><?php echo $resultOutraPessoa['nome']; if(!empty($resultNotificacao)){ echo "<sup><span class='badge badge-pill badge-danger'>!</span></sup></p>"; } ?></center>
								</div>
								<!-- /Card doação -->

<?php						}
						}
						else{ 
							if(empty(mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM notificacao WHERE id=" . $resultTransacao['itemPrimario'] . " AND remetente='" . $resultUsuario['cpfcnpj'] . "'")))){
								$resultPessoa1 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM usuario WHERE cpfcnpj='" . $resultTransacao['cpfcnpj1'] . "'"));
								$resultPessoa2 = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM usuario WHERE cpfcnpj='" . $resultTransacao['cpfcnpj2'] . "'"));
								$endereco1 = simplexml_load_file("http://viacep.com.br/ws/" . $resultPessoa1['cep'] . "/xml/");
								$endereco2 = simplexml_load_file("http://viacep.com.br/ws/" . $resultPessoa2['cep'] . "/xml/");
								$enderecoMapa1 = "http://maps.google.com/maps?q=" . $endereco1->logradouro . "+" .  $resultPessoa1['numero'] . "+" . $endereco1->bairro . "+" . $endereco1->localidade . "+" . $endereco1->uf . "&output=embed";
								$enderecoMapa2 = "http://maps.google.com/maps?q=" . $endereco2->logradouro . "+" .  $resultPessoa2['numero'] . "+" . $endereco2->bairro . "+" . $endereco2->localidade . "+" . $endereco2->uf . "&output=embed";
								$enderecoCompleto1 = $endereco1->logradouro . ", " . $resultPessoa1['numero'] . ", ";
								if($endereco1->complemento != ""){
									$enderecoCompleto1 = $enderecoCompleto1 . $endereco1->complemento;
								}
								$enderecoCompleto1 = $enderecoCompleto1 . " - " .  $endereco1->bairro . ", " . $endereco1->localidade . " - " . $endereco1->uf;
								$enderecoCompleto2 = $endereco2->logradouro . ", " . $resultPessoa2['numero'] . ", ";
								if($endereco2->complemento != ""){
									$enderecoCompleto2 = $enderecoCompleto2 . $endereco2->complemento;
								}
								$enderecoCompleto2 = $enderecoCompleto2 . " - " .  $endereco2->bairro . ", " . $endereco2->localidade . " - " . $endereco2->uf; ?>
							
								<!-- Card transporte -->
								<div id='<?php echo $resultTransacao['itemPrimario']; ?>' class="bg-dark px-2 my-3 rounded-lg transporte" style="cursor: pointer;">
									<input type="hidden" class="endPessoa1" value='<?php echo $enderecoCompleto1; ?>'>
									<input type="hidden" class="endPessoa2" value='<?php echo $enderecoCompleto2; ?>'>
									<input type="hidden" class="mapaPessoa1" value='<?php echo $enderecoMapa1; ?>'>
									<input type="hidden" class="mapaPessoa2" value='<?php echo $enderecoMapa2; ?>'>
									<input type="hidden" class="dataEntrega" value='<?php echo $resultTransacao['dataEntrega']; ?>'>
									<div class="row text-center font-weight-bold py-2">
										<div class="col">
											<svg width="3em" height="3em" viewBox="0 0 16 16" class="bi bi-truck" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  												<path fill-rule="evenodd" d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
											</svg>
										<div class="col">
											<img <?php 	if($resultPessoa1['fotoTipo'] == "site"){ 
                                    						echo "src='style/media/usericon" . bindec($resultPessoa1['fotoPerfil']) . ".png'";
                                						}
                                						else{
                                    						echo "src='data:" . $resultPessoa1['fotoTipo'] . ";base64," . base64_encode($resultPessoa1['fotoPerfil']) . "'";
                                						}?> class="rounded-circle fotoPessoa1" width="50" height="50"><p class="d-inline nomePessoa1"><?php echo $resultPessoa1['nome'] ?> </p>
											<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-arrow-left-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
 		 										<path fill-rule="evenodd" d="M1 11.5a.5.5 0 0 0 .5.5h11.793l-3.147 3.146a.5.5 0 0 0 .708.708l4-4a.5.5 0 0 0 0-.708l-4-4a.5.5 0 0 0-.708.708L13.293 11H1.5a.5.5 0 0 0-.5.5zm14-7a.5.5 0 0 1-.5.5H2.707l3.147 3.146a.5.5 0 1 1-.708.708l-4-4a.5.5 0 0 1 0-.708l4-4a.5.5 0 1 1 .708.708L2.707 4H14.5a.5.5 0 0 1 .5.5z"/>
											</svg>
											<img <?php	if($resultPessoa2['fotoTipo'] == "site"){ 
                                    						echo "src='style/media/usericon" . bindec($resultPessoa2['fotoPerfil']) . ".png'";
                                						}
                                						else{
                                    						echo "src='data:" . $resultPessoa2['fotoTipo'] . ";base64," . base64_encode($resultPessoa2['fotoPerfil']) . "'";
                                						}?> class="rounded-circle fotoPessoa2" width="50" height="50"><p class="d-inline nomePessoa2"> <?php echo $resultPessoa2['nome'] ?></p>
										</div>
									</div>
								</div>
								<!-- /Card transporte -->
<?php						}
						}
					} ?>
				</form>
<?php		}?>
		</div>
		<!--  -->


	</div>

	<script type='text/javascript' charset="utf-8">
		$(document).ready(function(){
<?php			if($numNotificacaoNova['COUNT(*)'] > 0){ ?>
					$("#transacaoNovaModal").modal();
<?php			}
				if($recusaTransacao == "s"){ ?>
					for(idNotifica = 1; $("#recusaTransacaoModal"+idNotifica)[0]; idNotifica++){
						$("#recusaTransacaoModal"+idNotifica).modal();
					}
<?php			} 
				if($cancelaTransacao == "s"){ ?>
					for(idNotifica = 1; $("#cancelaTransacaoModal"+idNotifica)[0]; idNotifica++){
						$("#cancelaTransacaoModal"+idNotifica).modal();
					}
<?php			}
				if($concluiTransacao == "s"){ ?>
					for(idNotifica = 1; $("#concluiTransacaoModal"+idNotifica)[0]; idNotifica++){
						$("#concluiTransacaoModal"+idNotifica).modal();
					}
<?php			}
				if($resultUsuario['transportador'] == "S"){ 
					if($numNotificacaoTransporte['COUNT(*)'] > 0){ ?>
						$("#transporteNovoModal").modal();
<?php				}?>
					var dataAtual = new Date();
					var dataManipulavel = new Date();

					dataManipulavel.setDate(dataManipulavel.getDate()-dataManipulavel.getDay());
					var inicioSemana = new Date(dataManipulavel.getFullYear(),dataManipulavel.getMonth(),dataManipulavel.getDate());
					dataManipulavel.setDate(dataManipulavel.getDate()+6);
					var fimSemana = new Date(dataManipulavel.getFullYear(),dataManipulavel.getMonth(),dataManipulavel.getDate());

					var stringMesAnoAtual = dataAtual.getFullYear()+'-';
            		if(dataAtual.getMonth() < 10){
                		stringMesAnoAtual = stringMesAnoAtual+'0';
            		}
					dataManipulavel.setDate(dataManipulavel.getDate()-dataManipulavel.getDay());
            		stringMesAnoAtual = stringMesAnoAtual+(dataAtual.getMonth()+1)+'-';
					var diasMes = new Date(dataAtual.getFullYear(),(dataAtual.getMonth()+1),0).getDate();
					var eventoPost = new FormData();
					eventoPost.append('Itsmesano',stringMesAnoAtual);
            		eventoPost.append('Itsdiasmes',diasMes);
					eventoPost.append('Itsoperacao','read');
					var eventoId;
					var dataId;
					var objHorarios = new Object();
					var contProp = 0;

					var transportadorPost = new FormData();
					transportadorPost.append("operacao","delete");
					transportadorPost.append("quemDeletou","transportador");
					var idTransporte;

					function alteraDiaSemana(dia, eventoOriginal){
						if($("#d"+dia).html() == "<b>"+dia+"</b>: Não está disponível<br>"){
							$("#d"+dia).html("<b>"+dia+"</b>:");
						}
						if(eventoOriginal[contEvento].id.length == 12){
							$("#d"+dia).html($("#d"+dia).html()+" ["+eventoOriginal[contEvento].title+"]<br>");
						}
						else{
							$("#d"+dia).html($("#d"+dia).html()+" Dia todo<br>");
						}
					}
					
					$.ajax({
                        method: 'POST',
                        url: 'readUpdateEvento.php',
                        data: eventoPost,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function (jason){
                            if(jason.erro){
                                window.Location.href = 'https://localhost/TCC/home.php';
                            }
							for(contEvento = 0; jason[contEvento].id; contEvento++){
								if(jason[contEvento].id.length == 12){
                                	eventoId = jason[contEvento].id.split('_').shift();
                            	}
								else{
									eventoId = jason[contEvento].id;
								}
								dataId = eventoId.split("-");
								dataManipulavel.setFullYear(dataId[0],dataId[1]-1,dataId[2]);
								if(Date.parse(eventoId) >= inicioSemana &&  Date.parse(eventoId) <= fimSemana){
									switch(dataManipulavel.getDay()){
										case 0:
											alteraDiaSemana("Dom",jason);
										break;
										case 1:
											alteraDiaSemana("Seg",jason);
										break;
										case 2:
											alteraDiaSemana("Ter",jason);
										break;
										case 3:
											alteraDiaSemana("Qua",jason);
										break;
										case 4:
											alteraDiaSemana("Qui",jason);
										break;
										case 5:
											alteraDiaSemana("Sex",jason);
										break;
										case 6:
											alteraDiaSemana("Sab",jason);
										break;
									}
								}
							}
						}
                    });

					$(".transporte").click(function(){
						idTransporte = $(this).attr("id");
						$("#foto1").attr("src",$(this).find(".fotoPessoa1").attr("src"));
						$("#foto2").attr("src",$(this).find(".fotoPessoa2").attr("src"));
						$("#nome1").html($(this).find(".nomePessoa1").html());
						$("#nome2").html($(this).find(".nomePessoa2").html());
						$("#mapa1").attr("src",$(this).find(".mapaPessoa1").val());
						$("#mapa2").attr("src",$(this).find(".mapaPessoa2").val());
						$("#end1").html($(this).find(".endPessoa1").val());
						$("#end2").html($(this).find(".endPessoa2").val());
						$("#dataEntrega").html("Data de entrega: "+$(this).find(".dataEntrega").val().substring(0,10)+"<br>Horário: "+$(this).find(".dataEntrega").val().substring(11,16));
						$("#transporteModal").modal();
		
					});

					if($("#cancelaTransporte").val() == "s"){
						for(idNotifica = 1; $("#cancelaTransporteModal"+idNotifica)[0]; idNotifica++){
							$("#cancelaTransporteModal"+idNotifica).modal();
						}
					}

					$("#btnCancelaTransporte").click(function(){
						$("#confirmCancelaTransporteModal").modal();
					});

					$("#confirmCancelaTransporte").click(function(){
						transportadorPost.append("transacao",idTransporte);
						$.ajax({
                        	method: 'POST',
                        	url: 'createDeleteTransportador.php',
                        	data: transportadorPost,
                        	contentType: false,
                        	processData: false,
                        	success: function (){
                            	$("#"+idTransporte).remove();
							},
							error: function(erro){
								alert(erro.responseText);
							}
                    	});
					});
			<?php
				}
			?>

			$(".transacao").click(function(){
				$("#idEscolhido").val($(this).find(".ids").val());
				$("#escolhaTransacao").submit();
			});

		});
	</script>
</body>
</html>