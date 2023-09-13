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

        //Upload Image
        if(isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {
            // print_r($_FILES); exit;

            $image = $_FILES["image"];
            $imageTypes = ["image/jpeg","image/jpg","image/png"];

            $ext = strtolower(substr($image['name'], -4));

            //Checagem de tipo de Image
            if(in_array($image["type"], $imageTypes)){

                //Checagem JPG
                if($ext == ".jpg" ?: $ext == ".jpeg"){
                    
                    $imageFile = imagecreatefromjpeg($image["tmp_name"]);
                
                //PNG
                } else if ($ext == ".png") {

                    $imageFile = imagecreatefrompng($image["tmp_name"]);

                } else {

                    $message->setMessage("Formato inválido de imagem!", "error", "back");

                }

                $imageName = $user->imageGenerateName($ext);

                imagejpeg($imageFile, "./img/users/" . $imageName, 100);

                $userData->image = $imageName;

            } else {

                $message->setMessage("Tipo inválido de arquivo!", "error", "back");

            }

        }

        $userDao->update($userData);
        
    //Atualizar senha do Usuário
    } else if($type === "changepassword"){

        //Preenche os dados do usuário
        $password = filter_input(INPUT_POST, "password");
        $confirmpassword = filter_input(INPUT_POST, "confirmpassword");

        //Dados do user
        $userData = $userDao->verifyToken();
        $id = $userData->id;

        if($password == $confirmpassword){

            //Criar o objeto usuário
            $user = new User();

            $finalPassword = $user->generateHashPassword($password);

            $user->password = $finalPassword;
            $user->id = $id;

            $userDao->changePassword($user);

        } else {
            $message->setMessage("As senhas não coencidem!", "error", "back");
        }

    } else {

        $message->setMessage("Dados invalidos!", "error", "index.php");

    }


?>