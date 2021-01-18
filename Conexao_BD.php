<?php

// Criando as variaveis

$servidor = "localhost";
$usuarioDB = "root";
$senha = ""; // Coloca se o seu phpMyAdmin tiver senha registrada.
$Dbname = "warehouse_data";

// Criando a conexão
$conn = mysqli_connect($servidor, $usuarioDB, $senha, $Dbname); 

// Testando a conexão
    if(!$conn){
        die("Falha na conexao: " . mysqli_connect_error());
    }     
?> 