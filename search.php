<?php
require_once("templates/header.php");
require_once("dao/MovieDAO.php");

//DAO Filmes
$movieDao = new MovieDAO($conn, $BASE_URL);

//Query do Usuário
$q = filter_input(INPUT_GET, "q");

$movies = $movieDao->findByTitle($q);

?>

<div id="main-container" class="container-fluid">
    <h2 class="section-title" id="search-title">Você está buscando por: <span id="search-result"><?= $q ?></span></h2>
    <p class="section-description">Resultados de busca retornados com base na sua pesquisa.</p>
    <div class="movies-container">

        <?php foreach ($movies as $movie) : ?>
            <?php require("templates/movie_card.php"); ?>
        <?php endforeach; ?>

        <?php if (count($movies) == 0) : ?>
            <p class="empty-list">Ainda não há filmes para esta busca, <a class="back-link" href="<?= $BASE_URL ?>" >voltar</a>!</p>
        <?php endif; ?>

    </div>
</div>

<?php

require_once("templates/footer.php");

?>