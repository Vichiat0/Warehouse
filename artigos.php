<!DOCTYPE html>
<html>
<head>
	<title>Artigos</title>
    
    <meta charset="utf-8">

    <!-- folahs de estilo -->
	<link rel="stylesheet" type="text/css" href="style/style-artigos.css">
	<link rel="stylesheet" type="text/css" href="style/style-warehouse.css">
	<link rel="stylesheet" type="text/css" href="style/bootstrap/css/bootstrap.min.css">

    <!-- kit de icones -->
    <script src="https://kit.fontawesome.com/c40cc3d328.js" crossorigin="anonymous"></script>


    <!-- javascripts do bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script type="text/javascript" src="style/bootstrap/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/parallax.js/1.4.2/parallax.min.js"></script>

    <script type="text/javascript">
        window.onscroll = function() {
            muda()
        };

        function muda() {
          if (document.body.scrollTop > 375 || document.documentElement.scrollTop > 375) {
            document.getElementById("navbar").style.backgroundColor="#FFFFFF";;
            document.getElementById("navbar").style.transition="0.5s";;
          } else {
            document.getElementById("navbar").style.backgroundColor = "transparent";
          }
        }
    </script>

</head>
<body scrolltop="muda('navbar')">
        
    <!-- navbar -->
    <nav class="fixed-top bg-light navbar navbar-light navbar-expand-lg m-3 rounded-pill py-0 px-4" id="navbar">
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
                <a class="nav-link txt20 active" href="artigos.php">Artigos</a>
            </li>

                <!-- ajuda -->
            <li class="nav-item mr-4">
                <a class="nav-link txt20" href="ajuda.php">Ajuda</a>
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







    <div id="mainSlider" class="carousel slide" data-ride="carousel">

        <ol class="carousel-indicators">
            <li data-target="#mainSlider" data-slide-to="0" class="active"></li>
            <li data-target="#mainSlider" data-slide-to="1"></li>
            <li data-target="#mainSlider" data-slide-to="2"></li>
        </ol>


        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="style/media/img6.jpg" class="d-block w-100" id="slide" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Conheça o movimento minimalista</h5>
                    <p>Quanto mais você doa ou troca, de menos coisas você precisa.</p>
                </div>
            </div>


            <div class="carousel-item">
                <img src="style/media/img4.jpg" class="d-block img-fluid w-100" id="slide" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Nem sempre precisamos comprar</h5>
                    <p>Podemos também trocar algo possuimos pelo o que queremos</p>
                </div>
            </div>


            <div class="carousel-item">
                <img src="style/media/img1.jpg" class="d-block img-fluid w-100" id="slide" alt="...">
                <div class="carousel-caption d-none d-md-block">
                    <h5>Doe!</h5>
                    <p> Algo guardado não te ajuda em nada, mas poderia estar ajudando outras pessoas.</p>
                </div>
            </div>


        </div>

        <a class="carousel-control-prev" href="#mainSlider" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#mainSlider" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>



    <div class="container-fluid my-5">

        <h1 class="font-weight-light text-secondary text-center m-5">Porque usar o warehouse?</h1>

        <div class="row m-5">
            <div class="col text-center">
                <img src="style/media/site.png" height="100" width="100" class="rounded-lg mb-2">
                <h4>Design Simples e Intuitivo</h4>
            </div>
            <div class="col text-center">                
                <img src="style/media/cadeado.png" height="100" width="100" class="rounded-lg mb-2">
                <h4>Segurança</h4>
            </div>
            <div class="col text-center">
                <img src="style/media/chat.png" height="100" width="100" class="rounded-lg mb-2">
                <h4>Para todas as idades</h4>
            </div>
        </div>
        <div class="row m-5">
            <div class="col text-center">
                <img src="style/media/reciclagem.png" height="100" width="100" class="rounded-lg mb-2">
                <h4>Bom Para o meio ambiente</h4>
            </div>
            <div class="col text-center">                
                <img src="style/media/doacao.png" height="100" width="100" class="rounded-lg mb-2">
                <h4>Incentivo a doação</h4>
            </div>
            <div class="col text-center">
                <img src="style/media/transporte.png" height="100" width="100" class="rounded-lg mb-2">
                <h4>Transporte Proprietário</h4>
            </div>
        </div>
    </div>













    <div>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="#fe5f55" fill-opacity="1" d="M0,64L60,80C120,96,240,128,360,149.3C480,171,600,181,720,160C840,139,960,85,1080,69.3C1200,53,1320,75,1380,85.3L1440,96L1440,320L1380,320C1320,320,1200,320,1080,320C960,320,840,320,720,320C600,320,480,320,360,320C240,320,120,320,60,320L0,320Z"></path></svg>
    </div>


    <div class="px-5 pb-5 bgprimary text-light">
        
    <h2 class="text-center font-weight-light pb-5 mb-5">Algumas das funcionalidades que o nosso site possui</h2>

    <div class="row">
        <div class="col">
            <div class="media">
                <img src="style/media/carpool.png" class="align-self-center mr-3 shadow bg-light p-3" style="border-radius: 24px;" width="150" height="150" alt="...">
                <div class="media-body">
                    <h5 class="mt-0">Modo Transportador</h5>
                    <p>Além de usar o nosso site para trocar ou doar, você também pode se voluntariar para virar um transportador.
                    Nesta função você pode transportar as transações de outras pessoas e como recompensa receberá um ticket.</p>
                </div>
            </div>
        </div>
        <div class="col">
        </div>
    </div>
    <div class="row">
        <div class="col">
        </div>
        <div class="col">
            <div class="media">
                <div class="media-body">
                    <h5 class="mt-0 mb-1">O que são tickets?</h5>
                    <p>Eles são a nossa forma de garantir que nenhum usuário tire proveito do site. Para você poder receber uma doação será necessário um ticket, o qual só poderá ser obtido ao realizar uma doação ou um transporte bem sucedido.</p>
                </div>
                <img src="style/media/cupom.png" height="150" width="150" class="ml-3 shadow bg-light p-3" style="border-radius: 24px;" alt="...">
            </div>
            
        </div>
        
    </div>
    <div class="row">
        <div class="col">
            <div class="media">
                <img src="style/media/warehouse.png" class="align-self-center mr-3 shadow bg-light p-3" style="border-radius: 24px;" width="150" height="150" alt="...">
                <div class="media-body">
                    <h5 class="mt-0">Estoque</h5>
                    <p>O estoque servirá para você adicionar os itens que deseja trocar ou doar, ficando expostos para outros usuários poderem vê-los e te contatar caso estejam interessados na proposta.</p>
                </div>
            </div>
        </div>
        <div class="col">
            
        </div>
        
    </div>
    <div class="row">
        <div class="col">
        </div>
        <div class="col">
            <div class="media">
                <div class="media-body">
                    <h5 class="mt-0 mb-1">Como funciona a transação?</h5>
                    <p>Após você contatar alguém em busca de um item, a pessoa em questão será notificada e poderá aceitar entrar em contato com você. Com isso será aberto uma seção onde poderão se comunicar e resolver sobre a transação em um chat. Após terem confirmado tudo poderão concluir a transação e avaliar a pessoa com quem transacionou.</p>
                </div>
                <img src="style/media/transacao.png" height="150" width="150" class="ml-3 shadow bg-light p-3" style="border-radius: 24px;" alt="...">
            </div>
            
        </div>
        
    </div>
</div>
        








    <div class="jumbotron jumbotron-fluid my-5" id="video">
        <div class="container text-center">
            <h1 class="">Veja como a plataforma funciona!</h1>
            <p class="lead">Assista a um video explicativo de como o nosso site funciona de maneira prática e rápida.</p>
            <hr>

            <div class="embed-responsive embed-responsive-16by9 mx-auto w-50 h-50">

                <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/ob2uHIiW3II" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                
            </div>
        </div>
    </div>


</body>
</html>