<?php
require_once("templates/header.php");

//Auth
require_once("dao/UserDAO.php");
require_once("models/User.php");
require_once("dao/MovieDAO.php");

$user = new User();
$userDao = new UserDAO($conn, $BASE_URL);

$userData = $userDao->verifyToken(true);

$movieDao = new MovieDAO($conn, $BASE_URL);

$id = filter_input(INPUT_GET, "id");

if (empty($id)) {

    $message->setMessage("O filme não foi encontrado!", "error", "index.php");
} else {

    $movie = $movieDao->findById($id);

    // Verifica se o filme existe
    if (!$movie) {

        $message->setMessage("O filme não foi encontrado!", "error", "index.php");
    }
}

// Checar se o filme tem imagem
if ($movie->image == "") {
    $movie->image = "movie_cover.jpg";
}

?>

<div id="main-container" class="container-fluid">
    <div class="col-md-12">
        <div class="row">
            <div class="col-md-6 offset-md-1 movie-container">
                <h1><?= $movie->title ?></h1>
                <p class="page-description">Altere os dados do filme no formulário abaixo:</p>
                <form id="edit-movie-form" action="<?= $BASE_URL ?>movie_process.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="type" value="update">
                    <input type="hidden" name="id" value="<?= $movie->id ?>">
                    <div class="form-group">
                        <label for="title">Título (*):</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Digite o título do seu filme" value="<?= $movie->title ?>">
                    </div>
                    <div class="form-group">
                        <label for="image">Imagem:</label><br>
                        <input type="file" class="form-control-file" id="image" name="image">
                    </div>
                    <div class="form-group">
                        <label for="length">Duração (*):</label>
                        <input type="text" class="form-control" id="length" name="length" placeholder="Digite a duração do filme" value="<?= $movie->length ?>">
                    </div>
                    <div class="form-group">
                        <label for="category">Categoria (*):</label>
                        <select name="category" id="category" class="form-control">
                            <option value="">Selecione</option>
                            <option value="Ação" <?= $movie->category === "Ação" ? "selected" : "" ?>>Ação</option>
                            <option value="Aventura" <?= $movie->category === "Aventura" ? "selected" : "" ?>>Aventura</option>
                            <option value="Drama" <?= $movie->category === "Drama" ? "selected" : "" ?>>Drama</option>
                            <option value="Comédia" <?= $movie->category === "Comédia" ? "selected" : "" ?>>Comédia</option>
                            <option value="Ficção" <?= $movie->category === "Ficção" ? "selected" : "" ?>>Ficção</option>
                            <option value="Fábula" <?= $movie->category === "Fábula" ? "selected" : "" ?>>Fábula</option>
                            <option value="Terror" <?= $movie->category === "Terror" ? "selected" : "" ?>>Terror</option>
                        </select>
                        <div class="form-group">
                            <label for="trailer">Trailer:</label>
                            <input type="text" class="form-control" id="trailer" name="trailer" placeholder="Insira o link do trailer" value="<?= $movie->trailer ?>">
                        </div>
                        <div class="form-group">
                            <label for="description">Descrição (*):</label>
                            <textarea name="description" id="description" cols="5" class="form-control" placeholder="Descreva resumidamente o filme..."> <?= $movie->description ?>"</textarea>
                        </div>
                        <input type="submit" value="Editar filme" class="btn card-btn">
                </form>
            </div>
        </div>
        <div class="col-md-4">
            <div id="editmovie-image-container" class="movie-image-container" style="background-image: url('<?= $BASE_URL ?>/img/movies/<?= $movie->image ?>')"></div>
        </div>
    </div>
</div>

<?php

require_once("templates/footer.php");

?>