<!-- Verifica se o usuário tá logado -->
<?php
	session_start();
	$_SESSION['pagina'] = "login";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<title>Entre na sua conta - Warehouse</title>
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"> -->

	<!-- Jquery -->
	<script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>

	<!-- Folhas de estilo -->
	<link rel="stylesheet" type="text/css" href="style/style-login.css">
	<link rel="stylesheet" type="text/css" href="style/bootstrap/css/bootstrap.css">
	<script type="text/javascript" src="js/bootstrap/bootstrap.js"></script>

	<!-- Verifica erro -->
	<script type="text/javascript" charset="utf-8">
		$(document).ready(function(){
			<?php 
            if(!empty($_SESSION['erroLogin'])){?>
                $('#erroModal').modal();
			<?php
            }
            ?>
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
                    <?php echo $_SESSION['erroLogin']; 
                        $_SESSION['erroLogin'] = "";?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>	

	<div class="position-fixed bg-dark p-5 w-30 h-80" id="container">


		<a href="home.php">
			<svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="white" xmlns="http://www.w3.org/2000/svg">
  				<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
			</svg>
		</a>

		<form action="Validacao.php" class="h-100 pb-4" method="POST">
			<center>


				<h1 class="text-light my-5">Login</h1>

				<div class="col my-4">

					<div class="row my-4">
						<input type="text" class="form-control input bg-dark rounded text-light" id="input" name="Itsemail" placeholder="Email">
					</div>

					<div class="row mb-5">
						<input type="password" class="form-control input bg-dark rounded text-light" id="input" name="senha" placeholder="Senha">
					</div>
						
					<button type="submit" class="btn w-50" id="btnSubmit">Entrar</button>


				</div>

				<a href="cadastro.php" class="btn text-light">Cadastre-se</a><br>
                <a href="recuperarSenha.html" class="btn text-light">Esqueceu sua senha?</a>

			</center>
		</form>
	</div>

</body>
</html>