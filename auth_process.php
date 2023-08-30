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

    //Verificador de Forms
    if($type === "register"){

        $name = filter_input(INPUT_POST, "name");
        $lastname = filter_input(INPUT_POST, "lastname");
        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");
        $confirmpassword = filter_input(INPUT_POST, "confirmpassword");

        //Verificação de dados mínimos
        if($name && $lastname && $email && $password){

            //Verificação de Senhas
            if($password === $confirmpassword){

                //Verificação de E-mail cadastrado no sistema (único)
                if($userDao->findByEmail($email) === false) {

                    $user = new User();

                    //Criação de Token e senha
                    $userToken = $user->generateToken();
                    $hashPassword = $user->generateHashPassword($password);

                    $user->name = $name;
                    $user->lastname = $lastname;
                    $user->email = $email;
                    $user->password = $hashPassword;
                    $user->token = $userToken;

                    $auth = true;

                    $userDao->create($user, $auth);

                } else{

                    //Avisar o erro de email repetido
                    $message->setMessage("Email já cadastrado!", "error", "back");
                }

            } else{

                //Avisar o erro de senha
                $message->setMessage("Senhas não coincidem!", "error", "back");

            }

        } else {

            //Enviar um erro com os dados que faltaram
            $message->setMessage("Por favor, preencha todos os campos!", "error", "back");

        }

    }else if($type === "login"){
        
        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");

        //Tenta autenticar o usuário
        if($userDao->authenticateUser($email, $password)){

            $message->setMessage("Seja bem-vindo!", "success", "editprofile.php");

        //Redireciona o usuário caso não consiga.    
        } else {

            //Enviar um erro com os dados que faltaram
            $message->setMessage("Usuário e/ou senha incorretos!", "error", "back");

        }

    } else {

        $message->setMessage("Dados invalidos!", "error", "index.php");

    }
?>