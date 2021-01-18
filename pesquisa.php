<!DOCTYPE html>
<html>
<head>
	<title>Resultados de busca</title>

	<!-- Folhas de estilo -->
	<link rel="stylesheet" type="text/css" href="style/style-warehouse.css">
	<link rel="stylesheet" type="text/css" href="style/bootstrap/css/bootstrap.css">

	<!-- Jquery -->
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>

    <!-- Script BootsTrap -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script type="text/javascript" src="js/bootstrap/bootstrap.js"></script>

</head>
<body>


<!-- Cabeçalho -->
<div class="row px-4 pt-3 bg-light" id="header">

    <!-- botão de voltar -->
	<div class="col-1">
		<a href="main.php">
    	<span>
        	<svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="gray" xmlns="http://www.w3.org/2000/svg">
            	<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
        	</svg>
    	</span>
    	</a>
    </div>        

    <!-- titulo da página -->
    <div class="col">
        <p class="text-secondary text-center" id="titleHeader">Resultados de pesquisa</p>
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








<!-- Barra de pesquisa -->
<div class="row my-4 justify-content-center">
	<input type="text" class="bg-light rounded-pill p-2 txt20 px-3 w-35 outline-none shadow" name="" placeholder="pesquise por algo aqui...">
</div>


<!-- conteudo de pesquisa -->
<div class="row m-5 text-center text-secondary">
	<div class="col">
		<h1>Resultados da pesquisa aparecem aqui</h1>
	</div>

</div>

<div class="row m-5">
	<div class="row">
		<div class="col">
			<a class="btn p-3 px-5 h-100 w-100 d-flex align-items-center justify-content-center bg-success rounded-24">
				<h5 class="text-light">Comida</h5>
			</a>
		</div>
		<div class="col">
			<a class="btn p-3 px-5 h-100 w-100 d-flex align-items-center justify-content-center bg-danger rounded-24">
				<h5 class="text-light">Roupas</h5>
			</a>
		</div>
		<div class="col">
			<a class="btn p-3 px-5 h-100 w-100 d-flex align-items-center justify-content-center bg-info rounded-24">
				<h5 class="text-light">Artigos tecnológicos</h5>
			</a>
		</div>
		<div class="col">
			<a class="btn p-3 px-5 h-100 w-100 d-flex align-items-center justify-content-center bg-warning rounded-24">
				<h5 class="text-light">Instrumentos musicais</h5>
			</a>
		</div>
		<div class="col">
			<a class="btn p-3 px-5 h-100 w-100 d-flex align-items-center justify-content-center bg-secondary rounded-24">
				<h5 class="text-light">Utensílios de cozinha</h5>
			</a>
		</div>
			
	</div>
		
</div>
	
	
</div>
</body>
</html>