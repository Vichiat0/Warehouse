<?php
    echo file_get_contents("http://viacep.com.br/ws/" . $_POST['cep'] . "/json/");
?>