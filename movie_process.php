<?php

require_once("globals.php");
require_once("db.php");
require_once("models/Movie.php");
require_once("models/Message.php");
require_once("dao/UserDAO.php");
require_once("dao/MovieDAO.php");

$message = new Message($BASE_URL);
$userDao = new UserDAO($conn, $BASE_URL);
$movieDao = new MovieDAO($conn, $BASE_URL);

//Tipo do Forms
$type = filter_input(INPUT_POST, "type");

//Verifica o Token e pega todas informações do Usuário
$userData = $userDao->verifyToken();

if ($type === "create") {

    //Recebendo os dados dos inputs
    $title = filter_input(INPUT_POST, "title");
    $description = filter_input(INPUT_POST, "description");
    $trailer = filter_input(INPUT_POST, "trailer");
    $category = filter_input(INPUT_POST, "category");
    $length = filter_input(INPUT_POST, "length");

    $movie = new Movie();

    //Validação mínima de dados
    if (!empty($title) && !empty($description) && !empty($category) && !empty($length)) {

        $movie->title = $title;
        $movie->description = $description;
        $movie->category = $category;
        $movie->length = $length;
        $movie->users_id = $userData->id;

        //Upload de img
        if (isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {

            $image = $_FILES["image"];
            $imageTypes = ["image/jpeg", "image/jpg", "image/png"];

            $ext = strtolower(substr($image['name'], -4));

            //Checagem de tipo de Image
            if (in_array($image["type"], $imageTypes)) {

                //Checagem JPG
                if ($ext == ".jpg" ?: $ext == ".jpeg") {

                    $imageFile = imagecreatefromjpeg($image["tmp_name"]);

                    //PNG
                } else if ($ext == ".png") {

                    $imageFile = imagecreatefrompng($image["tmp_name"]);
                } else {

                    $message->setMessage("Formato inválido de imagem!", "error", "back");
                }

                //Gerar nome da imagem
                $imageName = $movie->imageGenerateName($ext);

                imagejpeg($imageFile, "./img/movies/" . $imageName, 100);

                $movie->image = $imageName;
            }
        }

        $movieDao->create($movie);
    } else {

        $message->setMessage("Informações obrigatórias faltando!", "error", "back");
    }
} else if ($type === "delete") {

    //Recebendo os dados
    $id = filter_input(INPUT_POST, "id");

    $movie = $movieDao->findByID($id);

    if ($movie) {

        //Verificar se o filme é do usuário
        if ($movie->users_id === $userData->id) {

            $movieDao->destroy($movie->id);
        } else {

            $message->setMessage("Ação inválida!", "error", "index.php");
        }
    } else {

        $message->setMessage("Ação inválida!", "error", "index.php");
    }
} else if ($type === "update") {


    //Recebendo os dados dos inputs
    $title = filter_input(INPUT_POST, "title");
    $description = filter_input(INPUT_POST, "description");
    $trailer = filter_input(INPUT_POST, "trailer");
    $category = filter_input(INPUT_POST, "category");
    $length = filter_input(INPUT_POST, "length");
    $id = filter_input(INPUT_POST, "id");

    $movieData = $movieDao->findByID($id);

    //Verifica se encontrou um filme
    if ($movieData) {

        //Verificar se o filme é do usuário
        if ($movieData->users_id === $userData->id) {

            //Validação mínima de dados
            if (!empty($title) && !empty($description) && !empty($category) && !empty($length)) {

                //Edição do filme
                $movieData->title = $title;
                $movieData->description = $description;
                $movieData->trailer = $trailer;
                $movieData->category = $category;
                $movieData->length = $length;

                //Upload de img
                if (isset($_FILES["image"]) && !empty($_FILES["image"]["tmp_name"])) {

                    $image = $_FILES["image"];
                    $imageTypes = ["image/jpeg", "image/jpg", "image/png"];

                    $ext = strtolower(substr($image['name'], -4));

                    //Checagem de tipo de Image
                    if (in_array($image["type"], $imageTypes)) {

                        //Checagem JPG
                        if ($ext == ".jpg" ?: $ext == ".jpeg") {

                            $imageFile = imagecreatefromjpeg($image["tmp_name"]);

                            //PNG
                        } else if ($ext == ".png") {

                            $imageFile = imagecreatefrompng($image["tmp_name"]);
                        } else {

                            $message->setMessage("Formato inválido de imagem!", "error", "back");
                        }

                        //Gerar nome da imagem
                        $movie = new Movie();

                        $imageName = $movie->imageGenerateName($ext);

                        imagejpeg($imageFile, "./img/movies/" . $imageName, 100);

                        $movieData->image = $imageName;
                    }
                }

                $movieDao->update($movieData);

            } else {

                $message->setMessage("Informações obrigatórias faltando (título, descrição, categoria ou tamanho)!", "error", "back");
            }
        } else {

            $message->setMessage("Ação inválida!", "error", "index.php");
        }
    } else {

        $message->setMessage("Ação inválida!", "error", "index.php");
    }
} else {

    $message->setMessage("Informações inválidas!", "error", "index.php");
}
