function mostrarSenha(){
	var tipo = document.getElementById("inputPassword");
	if(tipo.type == "password"){
		tipo.type = "text";
	}else{
		tipo.type = "password";
	}
}

