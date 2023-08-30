<?php
    require_once("templates/header.php");

    require_once("dao/UserDAO.php");
    require_once("models/User.php");

    $user = new User();
    $userDao = new UserDAO($conn, $BASE_URL);

    $userData = $userDao->verifyToken(true);

    $fullName = $user->getFullName($userData);

    if($userData->image == ""){
        $userData->image = "user.png";
    }
?>

    <div id="main-container" class="container-fluid edit-profile-page">
        <div class="com-md-12">
            <form action="<?= $BASE_URL ?>user_process.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="type" value="update">
                <div class="row">
                    <div class="col-md-5">
                        <h1><?= $fullName ?></h1>
                        <p class="page-description">Altere seus dados no formulário abaixo:</p>
                        <div class="form-group">
                            <label for="name">Nome:</label>
                            <input type="text" class="form-control" name="name" id="name" placeholder="Digite o seu nome:" value="<?= $userData->name ?>" >
                        </div>
                        <div class="form-group">
                            <label for="lastname">Sobrenome:</label>
                            <input type="text" class="form-control" name="lastname" id="lastname" placeholder="Digite o seu sobrenome:" value="<?= $userData->lastname ?>" >
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="text" readonly class="form-control disabled" name="email" id="email" value="<?= $userData->email ?>" >
                        </div>
                        <input type="submit" class="btn card-btn" value="Alterar">
                    </div>
                    <div class="col-md-2">
                    </div>
                    <div class="col-md-5 ">
                        <div id="profile-image-container" style="background-image: url('<?= $BASE_URL ?>img/users/<?= $userData->image ?>')"></div>
                        <div class="form-group">
                            <label for="image">Foto:</label> <br>
                            <input type="file" class="form-control-file" name="image">
                        </div>
                        <div class="form-group">
                            <label for="bio">Biografia:</label>
                            <textarea class="form-control" name="bio" id="bio" rows="5" placeholder="Conte um pouco sobre você!">
                                <?= $userData->bio ?>
                            </textarea>
                        </div>
                    </div>
                </div>
            </form>
            <div class="row" id="change-password-container">
                <div class="col-md-4">
                    <h2>Alterar a Senha</h2>
                    <p class="page-description">Digite a nova senha e confirme!</p>
                    <form action="<?= $BASE_URL ?>user_process.php" method="post">
                        <input type="hidden" name="type" value="changepassword">
                        <div class="form-group">
                            <label for="password">Nova senha:</label>
                            <input type="password" class="form-control" name="password" id="password" placeholder="Digite a sua nova senha:">
                        </div>
                        <div class="form-group">
                            <label for="confirmpassword">Confirme a senha:</label>
                            <input type="password" class="form-control" name="confirmpassword" id="confirmpassword" placeholder="Digite a sua nova senha:">
                        </div>
                        <input type="submit" class="btn card-btn" value="Alterar Senha">
                    </form>
                </div>
            </div>
        </div>
    </div>

<?php

    require_once("templates/footer.php");
    
?>