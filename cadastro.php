<!-- Verifica se o usuário tá logado -->
<?php
	session_start();
	$_SESSION['pagina'] = "cadastro";
?>
<!DOCTYPE html>
<html>
<head>
	<title>Cadastre-se - Warehouse</title>
    <meta charset="utf-8">

    <!-- responsividade  -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!-- Jquery -->
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>
	<script type="text/javascript" src="js/jquery.mask.js"></script>

    <!-- Folhas de estilo -->
    <link rel="stylesheet" type="text/css" href="style/style-cadastro.css">
	<link rel="stylesheet" type="text/css" href="style/bootstrap/css/bootstrap.min.css">
	<script type="text/javascript" src="js/bootstrap.min.js"></script>

    <!-- Força da senha -->
	<script src="js/Forca_Senha.js"></script>

	<!-- Mostrar Senha -->
	<script src="js/Mostrar_Senha.js"></script>

	<!-- Validação -->	
	<script src="js/Validacao.js"></script>

    <!-- Mask -->

    <script type="text/javascript" charset="utf-8" async defer>
		$(document).ready(function(){
			var deuRuim = false;
			var campoId;
			var soma = 0;
			var resto = 0;
			var posBloco = 0;
			var cpfcnpj;
			var cepPost = new FormData();

			function validaCampos(campo){
				campoId = "#"+campo;
				switch(campo){
					case "inputNome": //VALIDA NOME -----------------------------------------
						//Verifica se o nome tem menos de 3 caracteres
						if($(campoId).val().length < 3){
            				$("#erroNome").html("Por favor, digite o seu nome inteiro.");
            				deuRuim = true;
        				}
						//Verifica se nome ta vazio
						if($(campoId).val() == ""){
            				$("#erroNome").html("Por favor, digite o seu nome.");
							deuRuim = true;
        				}
					break;

					case "inputEmail": //VALIDA EMAIL -----------------------------------------
						//Verifica se o e-mail possui ".com" ou "@" ou se ele está vazio
						if($(campoId).val().indexOf(".com") === -1 || $(campoId).val().indexOf("@") === -1 || $(campoId).val() == "") {
            				$("#erroEmail").html("Por favor, digite um e-mail válido.");
            				deuRuim = true;
        				}
					break;

					case "inputCpfCnpj": //VALIDA CPF/CNPJ -----------------------------------------------
						cpfcnpj = $(campoId).val().replace(/[^\d]/g,""); //Retira os traços, pontos e barras		
						//CPF				
						if($("#rdbCPF").prop("checked") == true){

							//Verifica se o CPF está completo ou é 00000000 ou 11111111 assim por diante
							if($(campoId).val().length < 14 || 
						   	   $(campoId).val() == "000.000.000-00" ||
						   	   $(campoId).val() == "111.111.111-11" ||
						       $(campoId).val() == "222.222.222-22" ||
						       $(campoId).val() == "333.333.333-33" ||
						       $(campoId).val() == "444.444.444-44" ||
						       $(campoId).val() == "555.555.555-55" ||
						       $(campoId).val() == "666.666.666-66" ||
						       $(campoId).val() == "777.777.777-77" ||
						       $(campoId).val() == "888.888.888-88" ||
						       $(campoId).val() == "999.999.999-99"){
						   		$("#erroCpfCnpj").html("Por favor, digite um CPF válido.");
            			   		deuRuim = true;
							}
							else{

								//Cálculo dos DVs do CPF

            					for (i = 1; i<=9; i++){ 
                					soma = soma + cpfcnpj.substr(i-1, 1) * (11 - i);
            					}

            					resto = soma % 11 < 2 ? 0 : 11 - (soma % 11);

            					if (resto != cpfcnpj.substr(9, 1)){
                					$("#erroCpfCnpj").html("Por favor, digite um CPF válido.");
                					deuRuim = true;
            					} 

            					soma = 0;

            					for (i = 1; i <= 10; i++){
                					soma = soma + cpfcnpj.substr(i-1, 1) * (12 - i);
            					}

            					resto = soma % 11 < 2 ? 0 : 11 - (soma % 11);

            					if (resto != cpfcnpj.substr(10, 1)){
                					$("#erroCpfCnpj").html("Por favor, digite um CPF válido.");
                					deuRuim = true;
            					}

								soma = 0;
							}
							//Verifica se o campo está vazio
							if($(campoId).val() == "") {
            					$("#erroCpfCnpj").html("Por favor, digite seu CPF.");
							}
						}
						//CNPJ
						else{

							if($(campoId).val().length < 18 || 
						   	   $(campoId).val() == "00.000.000/0000-00" ||
						   	   $(campoId).val() == "11.111.111/1111-11" ||
						       $(campoId).val() == "22.222.222/2222-22" ||
						       $(campoId).val() == "33.333.333/3333-33" ||
						       $(campoId).val() == "44.444.444/4444-44" ||
						       $(campoId).val() == "55.555.555/5555-55" ||
						       $(campoId).val() == "66.666.666/6666-66" ||
						       $(campoId).val() == "77.777.777/7777-77" ||
						       $(campoId).val() == "88.888.888/8888-88" ||
						       $(campoId).val() == "99.999.999/9999-99"){
						   		$("#erroCpfCnpj").html("Por favor, digite um CNPJ válido.");
            			   		deuRuim = true;
							}
							else{
								posBloco = 5;
            					soma = 0;

            					for (i= 1; i<=12; i++){
                					if((10 - posBloco) < 2){
                    					posBloco = 1;
                					}
                					soma = soma + cpfcnpj.substr(i-1, 1) * (10 - posBloco);
                					posBloco++;
            					}

            					resto = soma % 11 < 2 ? 0 : 11 - (soma % 11);

            					if (resto != cpfcnpj.substr(12,1)){
                					$("#erroCpfCnpj").html("Por favor, digite um CNPJ válido.");
                					deuRuim = true;
            					}

            					soma = 0;
            					posBloco = 4;
     
            					for (i= 1; i<=13; i++){
                					if((10 - posBloco) < 2){
                    					posBloco = 1;
                					}
                					soma = soma + cpfcnpj.substr(i-1, 1) * (10 - posBloco);
                					posBloco++;
            					}

            					resto = soma % 11 < 2 ? 0 : 11 - (soma % 11);

            					if (resto != cpfcnpj.substr(13,1)){
                					$("#erroCpfCnpj").html("Por favor, digite um CNPJ válido.");
                					deuRuim = true;
            					}
							}

							if($(campoId).val() == "") {
								$("#erroCpfCnpj").html("Por favor, digite seu CNPJ.");
								deuRuim = true;
							}

						}
					break;

					case "inputSenha": //VALIDA SENHA -----------------------------------------
						//Verifica se a senha está vazia
						if($(campoId).val() == "") {
            				$("#erroSenha").html("Por favor, digite sua senha.");
            				deuRuim = true;
        				}
						if($(campoId).val() != $("#inputConfirmSenha").val()){
							$("#erroConfirmSenha").html("Cuidado, senha e confirmação não estão idênticas.");
            				$("#inputConfirmSenha").attr("class","form-control campos is-invalid");
						}
					break;

					case "inputConfirmSenha": //VALIDA SENHA -----------------------------------
						//Verifica se a confirmação bate com a senha
						if($(campoId).val() != $("#inputSenha").val()){
							$("#erroConfirmSenha").html("Cuidado, senha e confirmação não estão idênticas.");
            				deuRuim = true;
						}
						//Verifica se a confirmação da senha está vazia
						if($(campoId).val() == "") {
            				$("#erroConfirmSenha").html("Por favor, confirme sua senha.");
            				deuRuim = true;
        				}
					break;

					case "inputCep": //VALIDA CEP -----------------------------------------------

						if($(campoId).val().length == 10){
							cepPost.append('cep',$("#inputCep").val().replace(/[^0-9]/g,""));
							$.ajax({
                    			method: 'POST',
                    			url: 'pesquisaCep.php',
                    			data: cepPost,
                    			contentType: false,
                    			processData: false,
                    			dataType: 'json',
                    			success: function (jason){
                        			if(jason.erro == true){
										$("#erroCep").html("Digite um CEP válido.");
										$(campoId).attr("class","form-control campos is-invalid");
										$("#inputUf").val("");
										$("#inputCidade").val("");
										$("#inputBairro").val("");
										$("#inputLogradouro").val("");
										$("#inputComplemento").val("");
									}
									else{
										$(campoId).attr("class","form-control campos is-valid");
										$("#inputUf").val(jason.uf);
										$("#inputCidade").val(jason.localidade);
										$("#inputBairro").val(jason.bairro);
										$("#inputLogradouro").val(jason.logradouro);
										$("#inputComplemento").val(jason.complemento);
									}
                    			} 
                			});
						}
						else{
							$("#erroCep").html("Digite um CEP válido.");
							$(campoId).attr("class","form-control campos is-invalid");
							$("#inputUf").val("");
							$("#inputCidade").val("");
							$("#inputBairro").val("");
							$("#inputLogradouro").val("");
							$("#inputComplemento").val("");
						}

					break;

					case "inputNumero": //VALIDA NÚMERO ------------------------------------
						if($(campoId).val() == ""){
							$("#erroNumero").html("Digite o número do local");
							deuRuim = true;
						}
						if($(campoId).val().match(/[^0-9]{2}/g)){
							$("#erroNumero").html("Digite um número válido");
							deuRuim = true;
						}
					break;

				}
				if($(campoId).attr("id") != "inputCep"){
					if(deuRuim == true){
            			$(campoId).attr("class","form-control campos is-invalid");
						deuRuim = false;
					}
					else{
						$(campoId).attr("class","form-control campos is-valid");
					}
				}
			}

			//Verifica se houve erro na validação
			<?php
			if(!empty($_SESSION['erroCadastro'])){?>
                $('#erroModal').modal();
			<?php
			}
			?>

			//Define masks iniciais
			$("#inputCep").mask("00.000-000");
		  	$("#inputCpfCnpj").mask("000.000.000-00");
			$("#inputNumero").mask("0AAAAA");

			//Alterna entre pessoa física e jurídica
			$(".cpfcnpj").click(function(){
				if($(this).attr("id") == "rdbCPF"){
					$("#inputCpfCnpj").mask("000.000.000-00");
					$('#inputCpfCnpj').attr('placeholder','Digite o seu CPF');
					$('#nome').html('Nome*');
					if($("#erroNome").html() == "Por favor, digite o nome inteiro da empresa."){
						$("#erroNome").html("Por favor, digite seu nome inteiro.");
					}
					if($("#erroNome").html() == "Por favor, digite o nome da empresa."){
						$("#erroNome").html("Por favor, digite o seu nome.");
					}
					$('#email').html('E-mail*');
					if($("#erroCep").html() == "Por favor, digite o CEP da empresa."){
						$("#erroCep").html("Por favor, digite seu CEP.");
					}
					$('#cod').html('CPF*');
				}
				else{
					$("#inputCpfCnpj").mask("00.000.000/0000-00");
					$('#inputCpfCnpj').attr('placeholder','Digite o seu CNPJ');
					$('#nome').html('Nome da empresa*');
					if($("#erroNome").html() == "Por favor, digite o seu nome inteiro."){
						$("#erroNome").html("Por favor, digite o nome inteiro da empresa.");
					}
					if($("#erroNome").html() == "Por favor, digite o seu nome."){
						$("#erroNome").html("Por favor, digite o nome da empresa.");
					}
					$('#email').html('E-mail corporativo*');
					if($("#erroCep").html() == "Por favor, digite seu CEP."){
						$("#erroCep").html("Por favor, digite o CEP da empresa.");
					}
					$('#cod').html('CNPJ*');
				}
				validaCampos("inputCpfCnpj");
			});

			//Valida campos antes de enviar (ainda permite envio)
			$(".campos").change(function(){
				$(this).val($(this).val().trim()); //Retira espaços brancos do início e do fim
				validaCampos($(this).attr("id"));
			});
		})
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
                    <?php echo $_SESSION['erroCadastro']; 
                        $_SESSION['erroCadastro'] = "";?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>

	<div class="p-3">
	<a href="login.php">
		<span>
			<svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="gray" xmlns="http://www.w3.org/2000/svg">
		  		<path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
			</svg>		
		</span>
	</a>
	</div>

	<h1 id="title">Cadastre-se aqui</h1>

	<!-- Formulario -->

	<form class="forms" id="form" method="POST" action="Validacao.php" accept-charset="utf-8" >

		<!-- Informações -->

		<!-- Campo de verificação -->


		
		<div class="container">

			<!-- 0 linha -->
		
			<!-- Tipo pessoa-->
			<div class="row">

				<div class="col">
				</div>

				<div class="custom-control custom-radio custom-control-inline">
					<input type="radio" id="rdbCPF" value="F" name="Itstipopessoa" class="custom-control-input cpfcnpj" checked>
					<label class="custom-control-label" for="rdbCPF" >Pessoa física</label>
				</div>
				<div class="custom-control custom-radio custom-control-inline">
					<input type="radio" id="rdbCNPJ" value="J" name="Itstipopessoa" class="custom-control-input cpfcnpj">
					<label class="custom-control-label" for="rdbCNPJ">Pessoa jurídica</label>
				</div>

				<div class="col">
				</div>
			</div>

			<!-- 1 linha -->
		
			<!-- Nome e Sobrenome-->
			<div class="row">
				
				<div class="col">
				
				</div>
				<div class="col-8">
					<div class="form-group">
						<label id="nome" for="inputNome">Nome*</label>
						<input type="text" class="form-control campos" id="inputNome" name="Itsname" pattern="[A-Za-záàâãéèêíïóôõöúçñÁÀÂÃÉÈÍÏÓÔÕÖÚÇÑ ]+$" placeholder="Digite o nome" autofocus aria-describedby="erroNome">
						<small id="erroNome" class="invalid-feedback"></small>
						
					</div>
				</div>
				

				<div class="col">
				</div>
			</div>


			<!-- 2 linha -->

			<!-- E-mail e Código (CPF/CNPJ)-->

				<div class="row">
					<div class="col">
					</div>

					<div class="col-4">
						<div class="form-group">
							<label id="email" for="inputEmail">E-mail*</label>
							<input type="text" class="form-control campos" id="inputEmail" name="Itsemail" placeholder="Digite o e-mail" aria-describedby="erroEmail">
							<small id="erroEmail" class="invalid-feedback"></small>
						</div>
					</div>

					<div class="col-4">
						<label id="cod" for="inputCpfCnpj">CPF*</label>
						<input type="text" class="form-control campos" id="inputCpfCnpj" name="Itscpfcnpj" placeholder="Digite o seu CPF" aria-describedby="erroCpfCnpj">
						<small id="erroCpfCnpj" class="invalid-feedback"></small>	
					</div>

					<div class="col">
					</div>
				</div>

			<!-- 3 linha -->

			<!-- Senha -->

			<div class="row">
				<div class="col">
				</div>

				<div class="col-4">
					<div class="form-group">
						<label for="inputPassword">Senha*</label>
						<input type="password" class="form-control campos" id="inputSenha" name="Itspassword" onkeyup=" validarSenhaForca()" placeholder="Digite a sua senha" aria-describedby="erroSenha">
						<small id="erroSenha" class="invalid-feedback"></small>
					</div>
				</div>

				<div class="col-4">
					<div class="form-group">
						<label for="inputConfirmPassword">Confirme a senha*</label>
						<input type="password" class="form-control campos" id="inputConfirmSenha" name="Itspassword2" placeholder="Confirme a sua senha" aria-describedby="erroConfirmSenha">
						<small id="erroConfirmSenha" class="invalid-feedback"></small>
					</div>
				</div>

				<div class="col">
				</div>
			</div>

			<!-- 4 linha -->

	        <!-- Barra de força da senha e mostrar senha-->

	        <div class="row">

	        	<div class="col-md-2">
	        		
	        	</div>
				<div class="col">
		            <div id="SenhaForca"></div>
	      		</div>
	      		<div class="col">
				</div>
				<div class="col-md-2">
					
				</div>
 
	        </div>

			<!-- 5 linha -->

	        <!-- CEP, Estado, Cidade e Bairro-->

			<div class="row">
				<div class="col">
				</div>

				<div class="col-2">
					<div class="form-group">
						<label for="inputCep">CEP*</label>
						<input type="text" class="form-control campos" id="inputCep" name="Itscep" placeholder="Digite o CEP" aria-describedby="erroCep">
						<small id="erroCep" class="invalid-feedback"></small>
					</div>
				</div>

				<div class="col-1">
					<div class="form-group">
						<label for="inputUf">UF</label>
						<input type="text" class="form-control campos" id="inputUf" disabled>
					</div>
				</div>

				<div class="col-2">
					<div class="form-group">
						<label for="inputCidade">Cidade</label>
						<input type="text" class="form-control campos" id="inputCidade" disabled>
					</div>
				</div>

				<div class="col-3">
					<div class="form-group">
						<label for="inputBairro">Bairro</label>
						<input type="text" class="form-control campos" id="inputBairro" disabled>
					</div>
				</div>

				<div class="col">
				</div>
			</div>

			<!-- 6 linha -->

			<!-- Logradouro, Número e Complemento-->
			
			<div class="row">
				<div class="col">
				</div>

				<div class="col-3">
					<div class="form-group">
						<label for="inputLogradouro">Logradouro</label>
						<input type="text" class="form-control campos" id="inputLogradouro" disabled>
					</div>
				</div>

				<div class="col-2">
					<div class="form-group">
						<label for="inputNumero">Número*</label>
						<input type="text" class="form-control campos" placeholder="Digite o número"id="inputNumero" name="Itsnumero" aria-describedby="erroNumero">
						<small id="erroNumero" class="invalid-feedback"></small>
					</div>
				</div>

				<div class="col-3">
					<div class="form-group">
						<label for="inputComplemento">Complemento</label>
						<input type="text" class="form-control campos" id="inputComplemento" disabled>
					</div>
				</div>

				<div class="col">
				</div>
			</div><br>

			<!-- Enviar-->       
                
			<div class="row">

				<div class="col">
					<input type="submit" name="" id="btnSubmit" class="btn btn-primary w-25 py-2 mx-auto btn-block" value="Enviar">
				</div>

			</div>
		</div>
	</form>
</body>
</html>