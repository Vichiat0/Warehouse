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
    $numFoto = 1;
    $nomeFoto = 1;
    $_SESSION['pagina'] = "config";
    $queryPesquisa = "SELECT * FROM usuario WHERE email = '$usuario' LIMIT 1";
    $resultUsuario = mysqli_fetch_assoc(mysqli_query($conn, $queryPesquisa));
    if(empty($resultUsuario)){ 
        $_SESSION['erroHome'] = "Não foi possível acessar os dados do usuário, tente reconectar-se";
        header("Location: home.php");
        die();
    }
    $resultCep = simplexml_load_file("http://viacep.com.br/ws/" . $resultUsuario['cep'] . "/xml/");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Configurações - Warehouse</title>

    <!-- responsividade -->
    <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Jquery -->
    <script type="text/javascript" src="js/jquery-3.5.1.min.js"></script>

    <!-- folhas de estilo -->
    <link rel="stylesheet" type="text/css" href="style/style-config.css">
    <link rel="stylesheet" type="text/css" href="style/style-warehouse.css">
    <link rel="stylesheet" type="text/css" href="style/bootstrap/css/bootstrap.css">
    <script type="text/javascript" src="js/bootstrap/bootstrap.js"></script>

    <!-- JqueryMask -->
    <script type="text/javascript" src="js/jquery.mask.js"></script>

    <!-- Full Calendar -->
    <link href='style/fullcalendar-scheduler/main.css' rel='stylesheet'>
    <script src='js/fullcalendar-scheduler/main.js'></script>
    <script src='js/fullcalendar-scheduler/lib/locales/pt-br.js'></script>

    <!-- Js script -->
    <script type="text/javascript" charset="utf-8" async defer>
        $(document).ready(function(){
        //INICIALIZAÇÃO DA PÁGINA -----------------------------------------------------------------------------------------

            // Mostra erro de validação
            <?php 
            if(!empty($_SESSION['erroConfig'])){?>
                $('#erroModal').modal();
            <?php
            }
            ?>

            //Declaração de variáveis e funções--------------------------------

            //Variáveis fixas
            var dataAtual = new Date(); //Recebe a data atual
            var stringMesAnoAtual = dataAtual.getFullYear()+"-";
            if(dataAtual.getMonth() < 10){
                stringMesAnoAtual = stringMesAnoAtual+"0";
            }
            stringMesAnoAtual = stringMesAnoAtual+(dataAtual.getMonth()+1)+"-"; //Recebe em formato YYYY-MM- ano e mês
            var diasMes = new Date(dataAtual.getFullYear(),(dataAtual.getMonth()+1),0).getDate(); //Recebe o último dia do mês
            var calendar; //Declara o objeto inicial do fullcalendar

            //Variáveis mutáveis
            var stringEventoClicado; //Recebe em formato YYYY-MM-DD o evento clicado

            var horario1; //Início de horário específico
            var inicioEvent; //Objeto Date que recebe horário1
            var horario2; //Fim de horário específico
            var fimEvent; //Objeto Date que recebe horário2

            var contEventoMesTodo = 0; //Contador de dias no mês com evento "dia todo"
            var verificaData = stringMesAnoAtual; //Contador de datas de eventos

            //Prepara atributos do POST dos eventos (ajax)
            var eventoPost = new FormData();
            eventoPost.append("Itsmesano",stringMesAnoAtual);
            eventoPost.append("Itsdiasmes",diasMes);
            eventoPost.append("Itsoperacao","read");
            var contEvento = 0;

            var renderizou = false;//Verifica se o calendário já renderizou ou não

            //Variáveis para validação dos campos de dados pessoais
            var campoId;
            var deuRuim = false;
            var soma = 0;
			var resto = 0;
			var posBloco = 0;
			var cnpj;
            var cepPost = new FormData();

            //Mask da placa do carro
            function selectPlaca(){
                if($("#tipoPlaca").prop("checked") == true){
                    $("#inputPlaca").mask("SSS-0000");
                }
                else{
                    $("#inputPlaca").mask("SSS0S00");
                }
            }

            //Validação de campos da seção dados pessoais
            function validaCampos(campo){
				campoId = "#"+campo;
				switch(campo){
					case "inputNome": //VALIDA NOME -----------------------------------------
						//Verifica se o nome tem menos de 3 caracteres
						if($(campoId).val().length < 3){
            				$("#erroNome").html("Por favor, digite o nome inteiro.");
            				deuRuim = true;
        				}
						//Verifica se nome ta vazio
						if($(campoId).val() == ""){
            				$("#erroNome").html("Por favor, digite o nome.");
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

					case "inputSenha": //VALIDA SENHA -----------------------------------------
						if($(campoId).val() != $("#inputConfirmSenha").val()){
							$("#erroConfirmSenha").html("Cuidado, senha e confirmação não estão idênticas.");
            				$("#inputConfirmSenha").attr("class","form-control campos is-invalid");
						}
					break;

					case "inputConfirmSenha": //VALIDA CONFIRMAÇÃO DA SENHA -----------------------------------
						//Verifica se a confirmação bate com a senha
						if($(campoId).val() != $("#inputSenha").val()){
							$("#erroConfirmSenha").html("Cuidado, senha e confirmação não estão idênticas.");
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



					case "inputCnpj": //VALIDA CNPJ -----------------------------------------------
						cnpj = $(campoId).val().replace(/[^\d]/g,""); //Retira os traços, pontos e barras		

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
						    $("#erroCnpj").html("Por favor, digite um CNPJ válido.");
            			   	deuRuim = true;
						}
						else{
							posBloco = 5;
            				soma = 0;

            				for (i= 1; i<=12; i++){
                				if((10 - posBloco) < 2){
                    				posBloco = 1;
                				}
                				soma = soma + cnpj.substr(i-1, 1) * (10 - posBloco);
                				posBloco++;
            				}

            				resto = soma % 11 < 2 ? 0 : 11 - (soma % 11);

            				if (resto != cnpj.substr(12,1)){
                				$("#erroCnpj").html("Por favor, digite um CNPJ válido.");
                				deuRuim = true;
            				}

            				soma = 0;
            				posBloco = 4;
     
            				for (i= 1; i<=13; i++){
                				if((10 - posBloco) < 2){
                    				posBloco = 1;
                				}
                				soma = soma + cnpj.substr(i-1, 1) * (10 - posBloco);
                				posBloco++;
            				}

            				resto = soma % 11 < 2 ? 0 : 11 - (soma % 11);

            				if (resto != cnpj.substr(13,1)){
                				$("#erroCnpj").html("Por favor, digite um CNPJ válido.");
                				deuRuim = true;
            				}
						}

						if($(campoId).val() == "") {
							$("#erroCnpj").html("Por favor, digite seu CNPJ.");
							deuRuim = true;
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

            //Coloca mask no CEP, no CPF e na placa do carro
            $("#inputCep").mask("00.000-000");
            $("#inputCNPJ").mask("00.000.000/0000-00");
            selectPlaca();

            //Pega o número de caracteres da descrição
            $("#caracteres").html("("+ (300 - $("#inputDescricao").val().length) + ")");

            //Verifica se modo transportador tá habilitado
            if($("#ativaTransportador").prop("checked") == true){
                eventoPost.append("Itstransportador","S");
                $("#divTransportador2").attr("class","");
            }
            else{
                eventoPost.append("Itstransportador","N");
                $("#divTransportador2").attr("class","d-none");
            }

        //EVENTOS USADOS PELO FULLCALLENDAR ---------------------------------------------------------------------------------------

            //Instancia o calendar com os dados do usuário usando a div fullcalendar inteira (por isso o uso de [0])
            calendar = new FullCalendar.Calendar($("#fullcalendar")[0],{

                schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives', //License key CC

                themeSystem: 'bootstrap', //Adapta ao tema do bootstrap

                height: 475, //Tamanho do calendário

                dayMinWidth: 125, //Largura das colunas do calendário

                initialView: 'dayGridMonth', //Tipo de visão do calendário (definido para dias do mês)

                showNonCurrentDates: false, //Retira datas de outros meses do início e fim do calendário

                locale: 'pt-br', //Define a linguagem

                //Declara como vai ser o cabeçalho do calendário
                headerToolbar:{
                    start: '',
                    center: 'title',
                    end:''
                },

                //Declara a formatação do título (nov. de 2020/novembro de dois mil e vinte)
                titleFormat:{
                    year: 'numeric', month: 'long'
                },

                selectable:true, //Habilita a seleção de dias

                //Recebe os eventos do BD
                events: function(info,successCallback,failureCallback){
                    $.ajax({
                        method: 'POST',
                        url: 'readUpdateEvento.php',
                        data: eventoPost,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function (jason){
                            if(jason.erro){
                                window.Location.href = "https://localhost/TCC/home.php";
                            }
                            successCallback(jason);
                            for(calendar.getEventById(jason[contEvento].id); calendar.getEventById(jason[contEvento].id); contEvento++){
                                evento = jason[contEvento].id;
                                eventoPost.append(evento.split("-").pop().split("-").pop()+"_id",jason[contEvento].id);
                                if(jason[contEvento].id.length == 12){
                                    eventoPost.append(evento.split("-").pop().split("-").pop()+"_horario1",jason[contEvento].title.substring(0,5));
                                    eventoPost.append(evento.split("-").pop().split("-").pop()+"_horario2",jason[contEvento].title.substring(6));
                                }
                                else{
                                    contEventoMesTodo++;
                                    if(contEventoMesTodo == diasMes){
                                        $("#disponibilidadeTotal").prop("checked",true).checkboxradio("refresh");
                                    }
                                }
                            }
                            contEvento = 0;
                        },
                        error: function(erro){
                            alert(erro.responseText);
                        }
                    });
                    
                },

                //Quando clica em uma data
                dateClick: function(info) {

                    data = info.date; //Recebe o objeto Date do evento clicado

                    stringEventoClicado = stringMesAnoAtual; //Recebe a string do atual mês e ano

                    //Formata para YYYY-MM-DD
                    if(data.getDate() < 10){
                        stringEventoClicado = stringEventoClicado+"0";
                    }
                    stringEventoClicado = stringEventoClicado+data.getDate();

                    //Chama o modal
                    $("#calendarioModalTitulo").html("Selecione sua disponibilidade no dia "+data.getDate());
                    //Vê se existe algum evento na data para marcar o radio e recuperar o horário
                    if(calendar.getEventById(stringEventoClicado) != null){
                        $("#disponivel2").prop('checked', true);
                    }
                    else{
                        for(verificaHorario = 1; verificaHorario <= 5; verificaHorario++){
                            if(calendar.getEventById(stringEventoClicado+"_"+verificaHorario)){
                                $("#disponivel3").prop('checked', true);
                            }
                        }
                        if($("#disponivel3").prop('checked') == false){
                            $("#disponivel1").prop('checked', true);
                        }
                    }
                    abreFechaHorarios($(".radioModal:checked"));
                    $('#calendarioModal').modal();
                }
            });

            //Cria o evento de dia todo no dia
            function criaDiaTodo(evento){
                if(calendar.getEventById(evento) == null){
                    calendar.addEvent({
                        id: evento,
                        start: evento,
                        color: 'light blue',
                        allDay: true
                    });
                    eventoPost.append(evento.split("-").pop().split("-").pop()+"_id",evento);
                    contEventoMesTodo++;
                    if(contEventoMesTodo == diasMes && $("#disponibilidadeTotal").prop("checked") == false){
                        $("#disponibilidadeTotal").prop("checked",true).checkboxradio("refresh");
                    }
                }
            }

            //Apaga o evento de dia todo no dia
            function apagaDiaTodo(evento){
                if(calendar.getEventById(evento) != null){
                    calendar.getEventById(evento).remove();
                    eventoPost.append(evento.split("-").pop().split("-").pop()+"_id",null);
                    contEventoMesTodo--;
                }
            }

            //Abre ou fecha aba de horários específicos
            function abreFechaHorarios(radioClicado){
                if(radioClicado.attr("id") == "disponivel3"){
                    $("#horariosEspecifico").attr("class","");
                    for(verificaHorario = 1; verificaHorario <= 5; verificaHorario++){
                        if(calendar.getEventById(stringEventoClicado+"_"+verificaHorario)){
                            $("#Itshour"+verificaHorario+"1").val(calendar.getEventById(stringEventoClicado+"_"+verificaHorario).title.split("-").shift());
                            $("#Itshour"+verificaHorario+"2").val(calendar.getEventById(stringEventoClicado+"_"+verificaHorario).title.split("-").pop());
                        }
                    }
                }
                else{
                    $("#horariosEspecifico").attr("class","d-none");
                    for(verificaHorario = 1; verificaHorario <= 5; verificaHorario++){
                        $("#Itshour"+verificaHorario+"1").val("");
                        $("#Itshour"+verificaHorario+"2").val("");
                    }
                }
            }

            //Apaga os 5 horários específicos agendados no dia
            function apagaHorariosEspecificos(evento){
                //Verifica se existe algum dos 5 horários
                for(verificaHorario = 1; verificaHorario <= 5; verificaHorario++){
                    if(calendar.getEventById(evento+"_"+verificaHorario) != null){
                        calendar.getEventById(evento+"_"+verificaHorario).remove();
                        eventoPost.append(evento.split("-").pop().split("-").pop()+"_"+verificaHorario+"_id",null);
                        eventoPost.append(evento.split("-").pop().split("-").pop()+"_"+verificaHorario+"_horario1",null);
                        eventoPost.append(evento.split("-").pop().split("-").pop()+"_"+verificaHorario+"_horario2",null);
                    }
                }
            }

            //Cria um horário específico agendado no dia
            function criaHorarioEspecifico(evento,id,inicio,fim,horario1,horario2){
                calendar.addEvent({
                    id: evento+"_"+id,
                    start: inicio,
                    end: fim,
                    title: horario1+"-"+horario2,
                    color: 'orange',
                    allDay: true
                })
                eventoPost.append(evento.split("-").pop().split("-").pop()+"_"+id+"_id",evento+"_"+id);
                eventoPost.append(evento.split("-").pop().split("-").pop()+"_"+id+"_horario1",horario1);
                eventoPost.append(evento.split("-").pop().split("-").pop()+"_"+id+"_horario2",horario2);
            }

        //EVENTOS -------------------------------------------------------------------------------------------------------

            //Troca seção
            $(".selectSecoes").click(function(){
                $(".divSecoes").attr("class","divSecoes d-none");
                $("div[name='"+$(this).attr('name')+"'").removeClass("d-none");
            });
            
            //Seleciona foto do site
            $(".fotoPreset").click(function (){
                $("#fotoDaora").attr("src","style/media/img.png");
                if($("#fotoUsuario")[0].files[0]){
                    $("#fotoUsuario").val(null);
                }
                $('#selectedProfile').attr("id","");
                $(this).attr("id","selectedProfile");
            });

            //Seleciona foto do computador
            $("#fotoUsuario").change(function (){
                if($(this)[0].files[0]){
                    if($('#selectedProfile:checked')){
                        $('#selectedProfile').prop("checked",false);
                        $('#selectedProfile').attr("id","");
                    }
                    $("#fotoDaora").attr("src",URL.createObjectURL($(this)[0].files[0]));
                }
                else{
                    $("#fotoDaora").attr("src","style/media/img.png");
                    $('img[name="foto1"]').attr("id","selectedProfile");
                    $('img[name="foto1"]').prop("checked",true);
                }
            });

            //Seleciona foto de plano de fundo
            $(".bgPreset").click(function (){
                $('#selectedBg').attr("id","");
                $(this).attr("id","selectedBg");
            });

            //Valida campos dos dados pessoais (ainda permite envio)
			$(".campos").change(function(){
				$(this).val($(this).val().trim()); //Retira espaços brancos do início e do fim
				validaCampos($(this).attr("id"));
			});

            //Atualiza número de caracteres da descrição
            $("#inputDescricao").on('input',function(){
                $("#caracteres").html("("+ (300 - $("#inputDescricao").val().length) + ")");
            });

            //Habilita/desabilita modo transportador
            $("#ativaTransportador").click(function (){
                if($(this).prop("checked") == true){
                    eventoPost.append("Itstransportador","S");
                    $(this).val("S");
                    $("#divTransportador2").attr("class","");
                }
                else{
                    $("#divTransportador2").attr("class","d-none");
                    $("#inputModelo").val("");
                    $("#inputPlaca").val("");
                    $("#inputCor").val("");
                    $(this).val("N");
                    eventoPost.append("Itstransportador","N");
                    for(verificaDia = 1; verificaDia <= diasMes; verificaDia++){
                        if(verificaDia < 10){
                            verificaData = verificaData+"0";
                        }
                        verificaData = verificaData+verificaDia;
                        apagaDiaTodo(verificaData);
                        apagaHorariosEspecificos(verificaData);
                        verificaData = stringMesAnoAtual;
                    }
                    if($("#disponibilidadeTotal").prop("checked") == true){
                        $("#disponibilidadeTotal").prop('checked', false).checkboxradio("refresh");
                    }
                }
            });

            //Define o tipo de placa do carro (antigo/novo)
            $("#tipoPlaca").click(function(){
                selectPlaca();
            });

            //Renderiza o calendário no site
            $("#habilitaCalendar").click(function(){
                if(renderizou == false){
                    $("#calendario").attr("class","");
                    calendar.render();
                    for(verificaDia = 1; verificaDia <= diasMes; verificaDia++){
                        if(verificaDia < 10){
                            verificaData = verificaData+"0";
                        }
                        verificaData = verificaData+verificaDia;
                        verificaData = stringMesAnoAtual;
                    }
                    renderizou = true;
                }
            });

            //Quando clica em disponível em qualquer dia ou horário do mês no calendário
            $("#disponibilidadeTotal").click(function(){
                if($(this).prop("checked") == true){
                    //Verifica se já existem eventos no calendário e os exclui
                    for(verificaDia = 1; verificaDia <= diasMes; verificaDia++){
                        if(verificaDia < 10){
                            verificaData = verificaData+"0";
                        }
                        verificaData = verificaData+verificaDia;
                        apagaHorariosEspecificos(verificaData);
                        criaDiaTodo(verificaData);
                        verificaData = stringMesAnoAtual;
                    }
                }
                else{
                    for(verificaDia = 1; verificaDia <= diasMes; verificaDia++){
                        if(verificaDia < 10){
                            verificaData = verificaData+"0";
                        }
                        verificaData = verificaData+verificaDia;
                        apagaDiaTodo(verificaData);
                        verificaData = stringMesAnoAtual;
                    }
                }
            });

            //Quando clica nos radios do modal do calendário
            $(".radioModal").click(function(){
                abreFechaHorarios($(this));
            });

            //Quando fecha o modal do calendário
            $('#calendarioModal').on('hidden.bs.modal', function (e) {

                //Não está disponível
                if($("#disponivel1").prop("checked") == true){
                    apagaDiaTodo(stringEventoClicado);
                    apagaHorariosEspecificos(stringEventoClicado);
                    //Desabilita disponível o mês todo
                    if($("#disponibilidadeTotal").prop("checked") == true){
                        $("#disponibilidadeTotal").prop('checked', false).checkboxradio("refresh");
                    }
                }

                //Disponível o dia todo
                if($("#disponivel2").prop("checked") == true){
                    apagaHorariosEspecificos(stringEventoClicado);
                    criaDiaTodo(stringEventoClicado);
                }

                //Disponível em horários específicos
                if($("#disponivel3").prop("checked") == true){
                    apagaDiaTodo(stringEventoClicado);
                    for(verificaHorario = 1; verificaHorario <= 5; verificaHorario++){
                        horario1 = $("#Itshour"+verificaHorario+"1").val();
                        horario2 = $("#Itshour"+verificaHorario+"2").val();
                        if(horario1 == "" || horario2 == "" || Date.parse(stringEventoClicado+"T"+horario1+":00") > Date.parse(stringEventoClicado+"T"+horario2+":00")){
                            if(calendar.getEventById(stringEventoClicado+"_"+verificaHorario) != null){
                                calendar.getEventById(stringEventoClicado+"_"+verificaHorario).remove();
                                eventoPost.append(stringEventoClicado.split("-").pop().split("-").pop()+"_"+verificaHorario+"_id",null);
                                eventoPost.append(stringEventoClicado.split("-").pop().split("-").pop()+"_"+verificaHorario+"_horario1",null);
                                eventoPost.append(stringEventoClicado.split("-").pop().split("-").pop()+"_"+verificaHorario+"_horario2",null);
                            }
                        }
                        else{
                            data.setHours(horario1.split(":").shift(),horario1.split(":").pop(),0);
                            inicioEvent = data;
                            data.setHours(horario2.split(":").shift(),horario2.split(":").pop(),0);
                            fimEvent = data;
                            if(calendar.getEventById(stringEventoClicado+"_"+verificaHorario) == null){
                                criaHorarioEspecifico(stringEventoClicado,verificaHorario,inicioEvent,fimEvent,horario1,horario2);
                            }
                            else{
                                calendar.getEventById(stringEventoClicado+"_"+verificaHorario).remove();
                                criaHorarioEspecifico(stringEventoClicado,verificaHorario,inicioEvent,fimEvent,horario1,horario2);
                            }
                        }
                    }
                    //Desabilita disponível o mês todo
                    if($("#disponibilidadeTotal").prop("checked") == true){
                        $("#disponibilidadeTotal").prop('checked', false).checkboxradio("refresh");
                    }
                }
                $(".radioModal:checked").prop("checked",false);
            });

            //Limpa o calendário
            $("#apagaAlteracoes").click(function(){
                for(verificaDia = 1; verificaDia <= diasMes; verificaDia++){
                    if(verificaDia < 10){
                        verificaData = verificaData+"0";
                    }
                    verificaData = verificaData+verificaDia;
                    apagaDiaTodo(verificaData);
                    apagaHorariosEspecificos(verificaData);
                    verificaData = stringMesAnoAtual;
                }
                if($("#disponibilidadeTotal").prop("checked") == true){
                    $("#disponibilidadeTotal").prop('checked', false).checkboxradio("refresh");
                }
            });

            //Quando clica no botão de apagar a conta
            $("#btnApagaConta").click(function() {
                $("#apagaContaValor").val("s");
                $('#apagaContaModal').modal();
            });

            //Quando fecha o modal de apagar a conta
            $('#apagaContaModal').on('hidden.bs.modal', function (e) {
                $("#apagaContaValor").val("n");
            });

            //Registra os dados do usuario no BD
            $("#salvarAlteracoes").click(function(){
                //Atualiza os eventos de transportador
                eventoPost.append('Itsoperacao',"update");
                $.ajax({
                    method: 'POST',
                    url: 'readUpdateEvento.php',
                    data: eventoPost,
                    contentType: false,
                    dataType: "json",
                    processData: false,
                    success: function (jason){
                        switch(jason.erro){
                            case "nenhum":
                                $("#formulario").submit();
                            break;
                            case "usuario":
                                window.location.href = "http://localhost/TCC/home.php";
                            break;
                            case "evento":
                                $("#msgErro").html(jason.msg);
                                $("#erroModal").modal();
                            break;
                        }
                    }
                });
            });
        })
    </script>

</head>
<body data-spy="scroll" data-target="#navbar-example">

    <!-- Modal de erro-->
    <div class="modal fade" id="erroModal" tabindex="-1" role="dialog" aria-labelledby="erroModalTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="erroModalTitulo">Erro</h5>
                </div>
                <div class="modal-body" id="msgErro">
                    <?php echo $_SESSION['erroConfig']; 
                        $_SESSION['erroConfig'] = "";?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal do calendário-->
    <div class="modal fade" id="calendarioModal" tabindex="-1" role="dialog" aria-labelledby="calendarioModalTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="calendarioModalTitulo">Selecione sua disponibilidade no dia 01</h5>
                </div>
                <div class="modal-body">
                    <input type="radio" class="radioModal" name="disponibilidade" id="disponivel1" value="1"> Não está disponível<br>
                    <input type="radio" class="radioModal" name="disponibilidade" id="disponivel2" value="2"> Disponível o dia todo<br>
                    <input type="radio" class="radioModal" name="disponibilidade" id="disponivel3" value="3"> Disponível em horários específicos<br>
                    <div id="horariosEspecifico" class="d-none">
                        Horário 1: <input type="time" id="Itshour11"> - Até - <input type="time" id="Itshour12"><br>
                        Horário 2: <input type="time" id="Itshour21"> - Até - <input type="time" id="Itshour22"><br>
                        Horário 3: <input type="time" id="Itshour31"> - Até - <input type="time" id="Itshour32"><br>
                        Horário 4: <input type="time" id="Itshour41"> - Até - <input type="time" id="Itshour42"><br>
                        Horário 5: <input type="time" id="Itshour51"> - Até - <input type="time" id="Itshour52"><br>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de deletar a conta-->
    <div class="modal fade" id="apagaContaModal" tabindex="-1" role="dialog" aria-labelledby="apagaContaModalTitulo" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="apagaContaModalTitulo">Deseja mesmo apagar sua conta?</h5>
                </div>
                <div class="modal-footer">
                    <button type="submit" form="formulario" class="btn btn-danger">Sim</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Não</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cabeçalho -->
    <div class="row px-4 pt-3 bg-light" id="header">

        <!-- botão de voltar -->
        <div class="col-5">
            <a style="cursor: pointer" onclick="window.history.back();">
                <span>
                    <svg width="2em" height="2em" viewBox="0 0 16 16" class="bi bi-arrow-left" fill="gray" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                    </svg>                
                </span>
            </a>
        </div>        

        <!-- titulo da página -->
        <div class="col">
            <p class="text-secondary" id="titleHeader">Configurações</p>
        </div>

        <div class="col-5">
        </div>

    </div>


    <!-- corpo inteiro da página -->
    <div class="m-4" id="navbar-example">
        <ul class="nav nav-tabs" role="tablist">

        <!-- menu de itens -->
        <div id="secaoAtual" class="list-group w-25 mr-5">
            <a class="list-group-item list-group-item-action selectSecoes" name="divPersonalizacao">Personalização</a>
            <a class="list-group-item list-group-item-action selectSecoes" name="divDados">Dados Pessoais</a>
            <a class="list-group-item list-group-item-action selectSecoes" name="divTransportador">Modo transportador</a>
            <a class="list-group-item list-group-item-action selectSecoes" name="divOutros">Outros</a>
        </div>
        <!--  -->


        <form id="formulario" class="w-70" action="Validacao.php" method="POST" enctype='multipart/form-data'>
            <div data-spy="scroll" data-target="#list-example" data-offset="0" class="scrollspy-example mx-2">

            	<!-- Seção  de personalização -->
                <div class="divSecoes" name="divPersonalizacao">
                    <h3 class="mb-5">Personalização</h3>


                    <!-- imagem de perfil -->
                    <label>Altere sua foto de perfil</label>
                    <center>
                    <div class="container">
                        
                        <label>
                            <img class="usericon fotoPreset" src="style/media/usericon1.png" name="foto1" <?php if(bindec($resultUsuario['fotoPerfil']) == 1){ echo "id='selectedProfile'"; } ?>>
                            <input type="radio" id="inputRadio" name="Itsperfil" value="1" <?php if(bindec($resultUsuario['fotoPerfil']) == 1){ echo "checked"; } ?> >
                        </label>
                        <label>
                        <img class="usericon fotoPreset" src="style/media/usericon2.png" name="foto2" <?php if(bindec($resultUsuario['fotoPerfil']) == 2){ echo "id='selectedProfile'"; } ?>>
                            <input type="radio" id="inputRadio" name="Itsperfil" value="2" <?php if(bindec($resultUsuario['fotoPerfil']) == 2){ echo "checked"; } ?>>
                        </label>
                        <label>
                        <img class="usericon fotoPreset" src="style/media/usericon3.png" name="foto3" <?php if(bindec($resultUsuario['fotoPerfil']) == 3){ echo "id='selectedProfile'"; } ?>>
                            <input type="radio" id="inputRadio" name="Itsperfil" value="3" <?php if(bindec($resultUsuario['fotoPerfil']) == 3){ echo "checked"; } ?>>
                        </label>
                        <label>
                        <img class="usericon fotoPreset" src="style/media/usericon4.png" name="foto4" <?php if(bindec($resultUsuario['fotoPerfil']) == 4){ echo "id='selectedProfile'"; } ?>>
                            <input type="radio" id="inputRadio" name="Itsperfil" value="4" <?php if(bindec($resultUsuario['fotoPerfil']) == 4){ echo "checked"; } ?>>
                        </label>
                        <label>
                        <img class="usericon fotoPreset" src="style/media/usericon5.png" name="foto5" <?php if(bindec($resultUsuario['fotoPerfil']) == 5){ echo "id='selectedProfile'"; } ?>>
                            <input type="radio" id="inputRadio" name="Itsperfil" value="5" <?php if(bindec($resultUsuario['fotoPerfil']) == 5){ echo "checked"; } ?>>
                        </label>
                        <label>
                        <img class="usericon fotoPreset" src="style/media/usericon6.png" name="foto6" <?php if(bindec($resultUsuario['fotoPerfil']) == 6){ echo "id='selectedProfile'"; } ?>>
                            <input type="radio" id="inputRadio" name="Itsperfil" value="6" <?php if(bindec($resultUsuario['fotoPerfil']) == 6){ echo "checked"; } ?>>
                        </label>
                    </div>
                    <div class="mx-auto">
                        <center>
                        <input type="file" accept='.jpg,.jpeg,.png' name="Itsfoto" id="fotoUsuario" class="custom-file-input inputFoto" lang="pt-br" data-multiple-caption="{count} files selected" multiple />
                        <label class="cursor-pointer" for="fotoUsuario"><img <?php if($resultUsuario['fotoTipo'] == "site"){ 
						                                                                echo "src='style/media/img.png'";
					                                                                }
					                                                                else{
						                                                                echo "src='data:" . $resultUsuario['fotoTipo'] . ";base64," . base64_encode($resultUsuario['fotoPerfil']) . "'";
					                                                                } ?> id="fotoDaora" height="150" width="150"><br><span>Selecione uma imagem sua</span></label>
                        </center>
                    </div>
                    </center>
                    <!--  -->

                    <!-- background do perfil -->
                    <br><br><label>Altere seu plano de fundo do perfil</label>
                    <div class="container bg-light rounded">

                        <label>
                            <img class="background m-2 bgPreset"src="style/media/presetbg1.jpg" name="bg1" <?php if($resultUsuario['fotoBg'] == 1){ echo "id='selectedBg'"; } ?>>
                            <input type="radio" id="inputRadio" name="Itsbg" value="1" <?php if($resultUsuario['fotoBg'] == 1){ echo "checked"; } ?>>
                        </label>
                        <label>
                            <img class="background m-2 bgPreset"src="style/media/presetbg2.jpg" name="bg2" <?php if($resultUsuario['fotoBg'] == 2){ echo "id='selectedBg'"; } ?>>
                            <input type="radio" id="inputRadio" name="Itsbg" value="2" <?php if($resultUsuario['fotoBg'] == 2){ echo "checked"; } ?>>
                        </label>
                        <label>
                            <img class="background m-2 bgPreset"src="style/media/presetbg3.jpg" name="bg3" <?php if($resultUsuario['fotoBg'] == 3){ echo "id='selectedBg'"; } ?>>
                            <input type="radio" id="inputRadio" name="Itsbg" value="3" <?php if($resultUsuario['fotoBg'] == 3){ echo "checked"; } ?>>
                        </label>
                        <label>
                            <img class="background m-2 bgPreset"src="style/media/presetbg4.jpg" name="bg4" <?php if($resultUsuario['fotoBg'] == 4){ echo "id='selectedBg'"; } ?>>
                            <input type="radio" id="inputRadio" name="Itsbg" value="4" <?php if($resultUsuario['fotoBg'] == 4){ echo "checked"; } ?>>
                        </label>
                            
            	    </div>

                </div>

                <!-- Seção dados pessoais -->
                <div class="d-none divSecoes" name="divDados">
                    <h3 class="mb-5">Dados Pessoais</h3>



                    <!-- Nome e Sobrenome -->
                    <div class="form-row mb-4">
                        <div class="col">
                            <label for="inputNome"><?php if($resultUsuario['tipoPessoa'] == "F"){
                                                            echo "Altere seu nome";
                                                         }
                                                         else{
                                                            echo "Altere o nome da empresa";
                                                         }?></label>
                            <input type="text" value="<?php echo $resultUsuario['nome'] ?>" class="form-control campos" placeholder="Nome" name="Itsname" id="inputNome" aria-describedby="erroNome">
                            <small id="erroNome" class="invalid-feedback"></small>
                        </div>

                    <!-- Email -->
                        <div class="form-group col-md-6">
                            <label for="inputEmail"><?php if($resultUsuario['tipoPessoa'] == "F"){
                                                            echo "Altere seu e-mail";
                                                         }
                                                         else{
                                                            echo "Altere o e-mail corporativo";
                                                         }?></label>
                            <input type="text" value="<?php echo $resultUsuario['email'] ?>" name="Itsemail" class="form-control campos" id="inputEmail" aria-describedby="erroEmail">
                            <small id="erroEmail" class="invalid-feedback"></small>
                        </div>
                    </div>

                    <!-- Senha -->
                    <div class="form-row mb-4">
                        <div class="col">
                            <label for="inputSenha">Troque sua senha</label>
                            <input type="password" class="form-control campos" name="Itspassword" id="inputSenha" aria-describedby="erroSenha">
                            <small id="erroSenha" class="invalid-feedback"></small>
                        </div>

                        <div class="col">
                            <label for="inputConfirmSenha">Confirme a Nova Senha</label>
                            <input type="password" class="form-control campos" name="Itspassword2" id="inputConfirmSenha" aria-describedby="erroConfirmSenha">
                            <small id="erroConfirmSenha" class="invalid-feedback"></small>
                        </div>
                    </div>

                    <!-- CEP -->
                    <div class="form-row my-2">
                        <div class="form-group col">
                            <label for="inputCep">Altere o CEP</label>
                            <input type="text" value="<?php echo $resultUsuario['cep'] ?>"class="form-control campos" name="Itscep" id="inputCep" aria-describedby="erroCep">
                            <small id="erroCep" class="invalid-feedback"></small>
                        </div>

                    <!-- CNPJ -->
                        <div <?php if($resultUsuario['tipoPessoa'] == "J"){
                                        echo "class='col'";
                                    }
                                    else{
                                        echo "class='col d-none'";
                                    }?>>
                            <label for='inputCnpj'>Altere seu CNPJ</label>
                            <input type='text' class="form-control campos" name='Itscnpj' value=<?php echo "'" . $resultUsuario['cpfcnpj'] . "'"; ?> id='inputCnpj' aria-describedby="erroCnpj">
                            <small id="erroCnpj" class="invalid-feedback"></small>
                        </div>
            	    </div>

                    <div class="form-row">
					    <div class="form-group col-2">
						    <label for="inputUf">UF</label>
						    <input type="text" value='<?php echo $resultCep->uf ?>' class="form-control campos" id="inputUf" disabled>
                        </div>
                        <div class="col-4">
						    <label for="inputCidade">Cidade</label>
						    <input type="text" value='<?php echo $resultCep->localidade ?>'class="form-control campos" id="inputCidade" disabled>
                        </div>
                        <div class="col-4">
						    <label for="inputBairro">Bairro</label>
						    <input type="text" value='<?php echo $resultCep->bairro ?>' class="form-control campos" id="inputBairro" disabled>
                        </div>
                        <div class="col-2">
						    <label for="inputNumero">Número</label>
						    <input type="text" value='<?php echo $resultUsuario['numero'] ?>'class="form-control campos" placeholder="Digite o número"id="inputNumero" name="Itsnumero" aria-describedby="erroNumero">
						    <small id="erroNumero" class="invalid-feedback"></small>
					    </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-6">
						    <label for="inputLogradouro">Logradouro</label>
						    <input type="text" value='<?php echo $resultCep->logradouro ?>' class="form-control campos" id="inputLogradouro" disabled>
                        </div>
                        <div class="form-group col-6">
						    <label for="inputComplemento">Complemento</label>
						    <input type="text" value='<?php echo $resultCep->complemento ?>' class="form-control campos" id="inputComplemento" disabled>
					    </div>
                    </div>
                    
                    <!-- Descrição -->
                    <div class="form-row mb-4">
                        <div class="col">
                            <label for="inputDescricao">Altere sua descrição</label><br>
                            <textarea class="w-100 pb-4 border" maxlength="300" name="Itsdescricao" id="inputDescricao"><?php echo $resultUsuario['descricao'] ?></textarea>
                            <a id="caracteres">(300)</a>
                        </div>
                    </div>

                </div>



                <!-- Seção transportador -->
                <div class="d-none divSecoes" name="divTransportador">
                    <h3 class="mb-5">Modo transportador</h3>

                    <div class="container my-4">
                        <!-- Habilitar modo de transporte -->
                        <div class="custom-control custom-switch">
                            <input type="checkbox"  class="custom-control-input" name="Itstransportador" id="ativaTransportador" <?php if($resultUsuario['transportador'] == "S"){
                                                                                                                                            echo "checked";
                                                                                                                                        }?>>
                            <label class="custom-control-label" for="ativaTransportador"><h5> Habilitar modo transportador</h5></label>
                        </div>
                    </div>

                    <div id="divTransportador2" class="d-none">
                        <div id="" class="row">
                                                  
                            <!-- campos do carro -->
                            <div class="form-group col">
                                <label for="inputModelo">Insira o modelo do automóvel</label>
                                <input type="text" class="form-control" name="Itsmodelo" id="inputModelo" value=<?php echo "'" . $resultUsuario['modeloCarro'] . "'"; ?>>
                            </div>

                            <div class="form-group col">
                                <label for="inputPlaca">
                                    Insira a placa do automóvel /
                                    <input type="checkbox" id="tipoPlaca" <?php if(preg_match_all("/-/",$resultUsuario['placaCarro'])){
                                                                                                                                echo "checked";
                                                                                                                            }?>> (Modelo antigo)
                                </label>
                                <input type="text" class="form-control" name="Itsplaca" id="inputPlaca" value=<?php echo "'" . $resultUsuario['placaCarro'] . "'"; ?>>
                            </div>

                        </div>

                        <label>Alterar a cor do automóvel</label>
                        <div id="" class="row m-3">
                            <!-- opções de cores -->
                            <div class="">        
                                <input type="color" class="rounded-circle" name="Itscor" id="corCarroValor" value=<?php echo "'";
                                                                            if($resultUsuario['corCarro']){
                                                                                echo $resultUsuario['corCarro'];
                                                                                $corCarro = $resultUsuario['corCarro'];
                                                                            }
                                                                            echo "'"; ?>>
                            </div>
                        </div>
                        <h3 for="fullcalendar" class="mt-5">Horário de disponibilidade</h3>
                        <p>
                            <div class="legenda" style="background-color: rgb(0, 132, 255);"></div> = Dia todo<br>
                            <div class="legenda" style="background-color: rgb(255, 174, 0);"></div> = Horários específicos
                        </p>
                        <button type="button" class="form-control bg-dark text-light mb-4 w-25" id="habilitaCalendar">Abrir sua agenda</button>
                        <div id="calendario" class="d-none">
                            <input type='checkbox' id='disponibilidadeTotal'> Total disponibilidade durante o mês <br><br>
                            <div id="fullcalendar">
                            </div>
                            <input class="form-control bg-danger text-light mb-4 w-25 mx-auto" type='button' value='Limpar calendário' id='apagaAlteracoes'>
                        </div>
                    </div>
                </div>

                <!-- Seção outros -->
                <div class="d-none divSecoes" name="divOutros">
                    <h3 class="mb-5">Outros</h3>
                    <h5>Outras configurações</h5>
                    <button type="button" class="btn btn-danger w-25 py-2 mt-2" id="btnApagaConta">Apague sua conta</button>
                    <input type="hidden" class="" id="apagaContaValor" name="apagaConta">
                </div>
                <!-- Botão de submit -->
                <center><button type="button" id="salvarAlteracoes" class="btn btn-primary w-25 py-2 mt-2">Salvar alterações</button></center>
            </div>
        </form>
    </ul>
  </div>
</body>
</html>