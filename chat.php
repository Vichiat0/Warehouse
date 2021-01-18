<!DOCTYPE html>
<html>
<head>
	<title>Chat</title>

	<!-- responsividade  -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <meta charset="utf-8">

    <!-- JQuery -->
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>

	<!-- Folhas de estilo -->
    <link rel="stylesheet" type="text/css" href="style/style-chat.css">
    <link rel="stylesheet" type="text/css" href="style/style-warehouse.css">
    <link rel="stylesheet" type="text/css" href="style/bootstrap/css/bootstrap.min.css">
    <script type="text/javascript" src="js/bootstrap/bootstrap.js"></script>
	<script type="text/javascript" src="js/bootstrap/bootstrap.bundle.js"></script>
</head>
<body>

<!-- Cabeçalho -->
<div class="row px-4 pt-3 bg-light" id="header2">

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
        <p class="text-secondary text-center" id="titleHeader">Chat</p>
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




<div class="container mx-auto shadow-lg rounded-lg bg-light m-5">
	<div class="row">
		



	
		<!-- Pessoas -->
		<div class="col-3 bglight">

				
			<h1 class="lead txt-primary text-center bg-light p-2 m-2 rounded-pill">Pessoas</h1>
					
				


			<div class="row bg-light d-flex align-items-center mb-1 p-2">
				<img src="style/media/pessoa2.jpg" height="60" width="60" class="rounded-circle">
				<p class="lead text-secondary m-0 ml-3">Julinha do Bock</p>
			</div>
			<div class="row bg-light d-flex align-items-center mb-1 p-2">
				<img src="style/media/pessoa4.jpg" height="60" width="60" class="rounded-circle">
				<p class="lead text-secondary m-0 ml-3">Juan Pelaes</p>
			</div>
			<div class="row bg-light d-flex align-items-center mb-1 p-2">
				<img src="style/media/pessoa1.jpg" height="60" width="60" class="rounded-circle">
				<p class="lead text-secondary m-0 ml-3">Alana Rodrigues</p>
			</div>
			<div class="row bg-light d-flex align-items-center mb-1 p-2">
				<img src="style/media/pessoa3.jpg" height="60" width="60" class="rounded-circle">
				<p class="lead text-secondary m-0 ml-3">Ludmilla Silva</p>
			</div>
		</div>
		<!-- /Pessoas -->




		<div class="col p-0 m-0 shadow">


			<!-- INFORMAÇÕES DA PESSOA -->
			<div class="row w-100 p-2 m-0 d-flex align-items-center justify-content-center">
				<img src="style/media/pessoa2.jpg" height="40" width="40" class="rounded-circle mr-3">
				<h5 class="">Julinha do Bock</h5>
			</div>
			<!-- /INFORMAÇÕES DA PESSOA -->





			<!-- Caixa de Mensagens -->
			<div class="row bg-light m-0" id="message-box">
				<div class="p-3 px-4 h-100 overflow-auto bglight w-100">





					<!-- MENSAGENS -->

					<div class="row">
						<h6 class="p-2 rounded-pill destinatario">Oiiiii
							<small class="destinatariosmall">10h30</small>
						</h6>
					</div>
					<div class="row">
						<h6 class="p-2 rounded-pill remetente">Oxi, te conheço peste?
							<small class="remetentesmall">10h30</small>
						</h6>
					</div><br>
					<div class="row">
						<h6 class="p-2 rounded-pill destinatario">Que isso menino. A gente fez ETEC juntos não lembra?
							<small class="destinatariosmall">10h30</small>
						</h6>
					</div>
					<div class="row">
						<h6 class="p-2 rounded-pill remetente"> Ataa, lembrei
							<small class="remetentesmall">10h30</small>
						</h6>
					</div>
					<div class="row">
						<h6 class="p-2 rounded-pill remetente">entao
							<small class="remetentesmall">10h30</small>
						</h6>
					</div>
					<div class="row">
						<h6 class="p-2 rounded-pill remetente">pois é
							<small class="remetentesmall">10h30</small>
						</h6>
					</div>
					<div class="row">
						<h6 class="p-2 rounded-pill remetente">ola
							<small class="remetentesmall">10h30</small>
						</h6>
					</div>
					<div class="row">
						<h6 class="p-2 rounded-pill destinatario">oi
							<small class="destinatariosmall">10h30</small>
						</h6>
					</div>
					<div class="row">
						<h6 class="p-2 rounded-pill remetente">entao
							<small class="remetentesmall">10h30</small>
						</h6>
					</div>
					<div class="row">
						<h6 class="p-2 rounded-pill destinatario">pois é
							<small class="destinatariosmall">10h30</small>
						</h6>
					</div>
					<div class="row">
						<h6 class="p-2 rounded-pill destinatario">Bora joga um free fire?
							<small class="destinatariosmall">10h30</small>
						</h6>
					</div>
					<div class="row">
						<h6 class="p-2 rounded-pill remetente">Arre égua. Jogo de corno. Deus me livre
							<small class="remetentesmall">10h30</small>
						</h6>
					</div>
					<div class="row">
						<h6 class="p-2 rounded-pill destinatario">Chatooo
							<small class="destinatariosmall">10h30</small>
						</h6>
					</div>
					
					<!-- /MENSAGENS -->





				</div>
			</div>
			<!-- /Caixa de Mensagens -->





			<!-- Caixa de Texto -->
			<div class="row bgwhite m-0 p-3 px-5 d-flex align-items-center">

			
				<div id="emojiLista" height=100 class="d-none">
					<span class="emojiSelect">😀</span>  
					<span class="emojiSelect">😃</span>  
					<span class="emojiSelect">😄</span>  
					<span class="emojiSelect">😁</span>
					<span class="emojiSelect">😆</span>
					<span class="emojiSelect">😅</span><br>
					<span class="emojiSelect">😂</span>
					<span class="emojiSelect">😉</span>
					<span class="emojiSelect">😊</span>
					<span class="emojiSelect">😇</span>
					<span class="emojiSelect">😍</span>
					<span class="emojiSelect">😘</span><br>
					<span class="emojiSelect">😗</span>
					<span class="emojiSelect">😚</span>
					<span class="emojiSelect">😙</span>
					<span class="emojiSelect">😋</span>
					<span class="emojiSelect">😛</span>
					<span class="emojiSelect">😜</span><br>
					<span class="emojiSelect">😝</span>
					<span class="emojiSelect">😐</span>
					<span class="emojiSelect">😑</span>
					<span class="emojiSelect">😶</span>
					<span class="emojiSelect">😏</span>
					<span class="emojiSelect">😒</span><br>
					<span class="emojiSelect">😬</span>
					<span class="emojiSelect">😌</span>
					<span class="emojiSelect">😔</span>
					<span class="emojiSelect">😪</span>
					<span class="emojiSelect">😡</span>
					<span class="emojiSelect">😴</span><br>
					<span class="emojiSelect">😷</span>
					<span class="emojiSelect">😵</span>
					<span class="emojiSelect">😠</span>
					<span class="emojiSelect">😎</span>
					<span class="emojiSelect">😕</span>
					<span class="emojiSelect">😟</span><br>
					<span class="emojiSelect">😮</span>
					<span class="emojiSelect">😯</span>
					<span class="emojiSelect">😲</span>
					<span class="emojiSelect">😳</span>
					<span class="emojiSelect">😦</span>
					<span class="emojiSelect">😧</span><br>
					<span class="emojiSelect">😨</span>
					<span class="emojiSelect">😰</span>
					<span class="emojiSelect">😥</span>
					<span class="emojiSelect">😢</span>
					<span class="emojiSelect">😭</span>
					<span class="emojiSelect">😱</span><br>
					<span class="emojiSelect">😖</span>
					<span class="emojiSelect">😣</span>
					<span class="emojiSelect">😞</span>
					<span class="emojiSelect">😓</span>
					<span class="emojiSelect">😩</span>
					<span class="emojiSelect">😫</span><br>
					<span class="emojiSelect">😤</span>
				</div>		
				<button id="emojiBotao" class="btn m-0 p-0">
					<svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-emoji-heart-eyes" fill="#fe5f55" xmlns="http://www.w3.org/2000/svg">
  						<path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
  						<path fill-rule="evenodd" d="M11.315 10.014a.5.5 0 0 1 .548.736A4.498 4.498 0 0 1 7.965 13a4.498 4.498 0 0 1-3.898-2.25.5.5 0 0 1 .548-.736h.005l.017.005.067.015.252.055c.215.046.515.108.857.169.693.124 1.522.242 2.152.242.63 0 1.46-.118 2.152-.242a26.58 26.58 0 0 0 1.109-.224l.067-.015.017-.004.005-.002zM4.756 4.566c.763-1.424 4.02-.12.952 3.434-4.496-1.596-2.35-4.298-.952-3.434zm6.488 0c1.398-.864 3.544 1.838-.952 3.434-3.067-3.554.19-4.858.952-3.434z"/>
					</svg>
				</button>


				<input type="text" id="mensagem" class="form-control rounded-pill w-75 mx-auto" placeholder="escreva algo..." style="">


					
				<button class="btn m-0 p-0">
					<svg class="d-none" width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-arrow-up-square" fill="#fe5f55" xmlns="http://www.w3.org/2000/svg">
  					<path fill-rule="evenodd" d="M14 1H2a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/>
  					<path fill-rule="evenodd" d="M8 12a.5.5 0 0 0 .5-.5V5.707l2.146 2.147a.5.5 0 0 0 .708-.708l-3-3a.5.5 0 0 0-.708 0l-3 3a.5.5 0 1 0 .708.708L7.5 5.707V11.5a.5.5 0 0 0 .5.5z"/>
					</svg>

					<svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-arrow-up-square-fill" fill="#fe5f55" xmlns="http://www.w3.org/2000/svg">
  					<path fill-rule="evenodd" d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm6.5 11.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V11.5z"/>
					</svg>
				</button>
			</div>
			<!-- /Caixa de Texto -->



		</div>
	</div>
	
</div>

<script type='text/javascript'>
	$(function(){
		$("#emojiBotao").popover({
			html: true,
			content: function(){
				var teste = $("#emojiLista").html();
				return teste; 
			},
			trigger: 'click',
			placement:"top",
			sanitize: false
		});
		$(document).on("click", ".emojiSelect", function() {
    		$("#mensagem").val($("#mensagem").val()+$(this).html());
		});
	})
</script>

</body>
</html>