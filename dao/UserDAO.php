<?php

    require_once("models/User.php");
    require_once("models/Message.php");

    class UserDAO implements userDAOInterface{

        private $conn;
        private $url;
        private $message;

        public function __construct(PDO $conn, $url){
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }

        public function buildUser($data){

            $user = new User();

            $user->id = $data["id"];
            $user->name = $data["name"];
            $user->lastname = $data["lastname"];
            $user->email = $data["email"];
            $user->password = $data["password"];
            $user->image = $data["image"];
            $user->bio = $data["bio"];
            $user->token = $data["token"];

            return $user;

        }
        public function create(User $user, $authUser = false){

            $stmt = $this->conn->prepare("INSERT INTO users(
                name, lastname, email, password, token)
                VALUES ( :name, :lastname, :email, :password, :token
                )");

            $stmt->bindParam(":name", $user->name);
            $stmt->bindParam(":lastname", $user->lastname);
            $stmt->bindParam(":email", $user->email);
            $stmt->bindParam(":password", $user->password);
            $stmt->bindParam(":token", $user->token);

            $stmt->execute();

            //Autenticação de Usuário (auth = true);
            if ($authUser) {
                $this->setTokenToSession($user->token);
            }
        }
        public function update(User $user, $redirect = true){

            $stmt = $this->conn->prepare("UPDATE users SET
            name = :name,
            lastname = :lastname,
            email = :email,
            image = :image,
            bio = :bio,
            token = :token
            WHERE id = :id
            ");

            $stmt->bindParam(":name", $user->name);
            $stmt->bindParam(":lastname", $user->lastname);
            $stmt->bindParam(":email", $user->email);
            $stmt->bindParam(":image", $user->image);
            $stmt->bindParam(":bio", $user->bio);
            $stmt->bindParam(":token", $user->token);
            $stmt->bindParam(":id", $user->id);

            $stmt->execute();

            if ($redirect) {
                //Vai até o perfil do usuário
                $this->message->setMessage("Dados atualizados com sucesso!", "success", "editprofile.php");
            }

        }
        
        public function verifyToken($protected = false){
            
            if(!empty($_SESSION["token"])){

                //Pega o Token da Session
                $token = $_SESSION["token"];

                $user = $this->findByToken($token);

                if($user){
                    return $user;
                } else if($protected) {

                    //Redireciona usuário não autenticado
                    $this->message->setMessage("Token invalido!", "error", "index.php");
                    
                }

            } else if($protected) {

                //Redireciona usuário não autenticado
                $this->message->setMessage("Logue para concluir esta ação!", "error", "index.php");
                
            }
        }
        public function setTokenToSession($token, $redirect = true){
            
            //Salvar Token na Session do Navegador
            $_SESSION["token"] = $token;

            if ($redirect) {
                //Vai até o perfil do usuário
                $this->message->setMessage("Seja bem-vindo!", "success", "editprofile.php");
            }
        }
        public function authenticateUser($email, $password){

            $user = new User();
        
            $user = $this->findByEmail($email);

            if($user){

                //Checar se as senhas batem
                if(password_verify($password, $user->password)){

                    //Gerar um token e inserir na sessão
                    $token = $user->generateToken();

                    $this->setTokenToSession($token, false);

                    //Atualizar o token do Usuário
                    $user->token = $token;

                    $this->update($user, false);

                    return true;

                } else {
                    return false;
                }

            } else {
                return false;
            }

        }
        public function findByEmail($email){

            if($email != ""){

                $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = :email");
                $stmt->bindParam(":email", $email);
                $stmt->execute();

                if($stmt->rowCount() > 0){

                    $data = $stmt->fetch();
                    $user = $this->buildUser($data);

                    return $user;

                } else{
                    return false;
                }

            } else{
                return false;
            }

        }
        public function findById($id){

        }

        public function findByToken($token){
            if($token != ""){

                $stmt = $this->conn->prepare("SELECT * FROM users WHERE token = :token");
                $stmt->bindParam(":token", $token);
                $stmt->execute();

                if($stmt->rowCount() > 0){

                    $data = $stmt->fetch();
                    $user = $this->buildUser($data);

                    return $user;

                } else{
                    return false;
                }

            } else{
                return false;
            }

        }

        public function destroyToken(){
            //Remove o Token da Session
            $_SESSION["token"] = "";

            //Mensagem de sucesso e redirecionamento
            $this->message->setMessage("Você foi desconectado com sucesso!", "success", "index.php");
        }

        public function changePassword(User $user){

        }
    }