<?php

    require_once("globals.php");
    require_once("db.php");
    require_once("models/User.php");
    require_once("models/Message.php");
    require_once("dao/UserDAO.php");

    $message = new Message($BASE_URL);

    $userDao = new UserDAO($conn, $BASE_URL);

    //Resgata o tipo do Forms
    $type = filter_input(INPUT_POST, "type"); //Ele retorna o 'type' do forms (register ou login)

    //Atualizar usuário
    if ($type === "update") {
        
        //Verifica o Token e pega todas informações do Usuário
        $userData = $userDao->verifyToken();

        //Receber Dados do Post
        $name = filter_input(INPUT_POST, "name");
        $lastname = filter_input(INPUT_POST, "lastname");
        $email = filter_input(INPUT_POST, "email");
        $bio = filter_input(INPUT_POST, "bio");

        //Cria um novo obj de usuário
        $user = new User();

        //Preenche os dados do usuário
        $userData->name = $name;
        $userData->lastname = $lastname;
        $userData->email = $email;
        $userData->bio = $bio;

        $userDao->update($userData);
        
    //Atualizar senha do Usuário
    } else if($type === "changepassword"){

    } else {

        $message->setMessage("Dados invalidos!", "error", "index.php");

    }


?>