function C_Senha(){

	var c_senha = document.getElementById('inputConfirmPassword').value;
	var senha = document.getElementById('inputPassword').value;

	if( c_senha ==  senha){
		return true;
	}
	else{
		alert('As senhas digitadas não são identicas');
		return false
	}
}