<!DOCTYPE html>
<html>
<head>
	<title>Warehouse - A plataforma de trocas e doações</title>

    <meta charset="UTF-8">
    
    <!-- Jquery -->
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>

    <!-- Script BootsTrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script type="text/javascript" src="js/bootstrap/bootstrap.js"></script>

    <!-- Folhas de estilo -->
    <link rel="stylesheet" type="text/css" href="style/style-home.css">
    <link rel="stylesheet" type="text/css" href="style/style-warehouse.css">
    <link rel="stylesheet" type="text/css" href="style/bootstrap/css/bootstrap.min.css">

	<!-- responsividade  -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Script Js -->
    <script type='text/javascript'>
        $(document).ready(function(){

            // Mostra dados de desconexão
            <?php 
            session_start();
            if(!empty($_SESSION['erroHome'])){
                echo "$('#erroModal').modal();";
            }
            ?>

            //Habilita o tooltip do login
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>

</head>
<body>

    <!-- Modal de erro-->
    <div class="modal fade" id="erroModal" tabindex="-1" role="dialog" aria-labelledby="erroModalTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class='modal-header'>
                    <h5 class='modal-title' id='erroModalTitulo'>Erro</h5>
                </div>
                <div class="modal-body">
                    <?php echo $_SESSION['erroHome']; 
                        $_SESSION['erroHome'] = "";?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <!-- navbar -->
	<nav class="navbar navbar-expand-lg m-3 px-5">
		<!-- marca -->
		<div class="mr-auto d-flex align-items-center">
			<a class="navbar-brand" href="#">
    			<img src="style/media/logo.png" width="70" height="70" alt="" loading="lazy">
  			</a>

    		<a class="navbar-brand" id="logoTitle" href="home.php">Warehouse</a>
		</div>

		<!-- Links da navbar -->
    	<ul class="navbar-nav d-flex align-items-center">
    			
    		<!-- home -->
      		<li class="nav-item mr-4 active">
        		<a class="nav-link text-light txt20" href="#">Home <span class="sr-only">(current)</span></a>
      		</li>

      		<!-- artigos -->
      		<li class="nav-item mr-4">
        		<a class="nav-link text-light txt20" href="artigos.php">Artigos</a>
      		</li>

      			<!-- ajuda -->
      		<li class="nav-item mr-4">
        		<a class="nav-link text-light txt20" href="ajuda.php">Ajuda</a>
      		</li>

      		<!-- créditos -->
      		<li class="nav-item mr-4">
        		<a class="nav-link text-light txt20" href="creditos.php">Créditos</a>
      		</li>

          <li class="nav-item">
            <a class="nav-link" href="login.php">
              <img src="style/media/usericon1.png" width="70" title="Login" data-toggle="tooltip" height="70" alt="" loading="lazy">
            </a>
          </li>

    	</ul>
	</nav>
    <!-- navbar end -->









    <div class="row mt-5">


        <div class="col">
        </div>

        <div class="col mt-5">
            <div class="row my-5">
                <div class="col text-center">
                <p class="display-4 txt-light d-inline"><b class="txt-primary">Desapegue</b> do que</p>
                <p class="display-4 txt-light d-inline" id="">você não usa mais!</p>
                </div>

            </div>

            <div class="row">
                <div class="col text-light mx-5 p-3">  
                    <center>
                    <p class="txt24">Warehouse é um site que conecta pessoas que querem trocar ou doar objetos.</p>

                    <a href="artigos.php#video" class="btn mt-3 p-2 px-5 rounded-pill" id="btnSubmit">Saiba mais</a>
                    </center>  
                </div>
                
            </div>

        </div>


    </div>
        
    </div>








</body>
</html>