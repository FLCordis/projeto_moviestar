<?php
require_once("models/Message.php");
require_once("models/Review.php");
require_once("dao/UserDAO.php");

    class ReviewDao implements ReviewDAOInterface {

        private $conn;
        private $url;
        private $message;

        public function __construct(PDO $conn, $url) {
            $this->conn = $conn;
            $this->url = $url;
            $this->message = new Message($url);
        }

        public function buildReview($data) {

            $reviewObject = new Review();

            $reviewObject->id = $data["id"];
            $reviewObject->rating = $data["rating"];
            $reviewObject->review = $data["review"];
            $reviewObject->users_id = $data["users_id"];
            $reviewObject->movies_id = $data["movies_id"];
            
            return $reviewObject;
        }
        public function create(Review $review) {

            $stmt = $this->conn->prepare("INSERT INTO reviews
            (rating, review, movies_id, users_id) VALUES 
            (:rating, :review, :movies_id, :users_id)");
            
            $stmt->bindParam(":rating", $review->rating);
            $stmt->bindParam(":review", $review->review);
            $stmt->bindParam(":movies_id", $review->movies_id);
            $stmt->bindParam(":users_id", $review->users_id);


            $stmt->execute();

            $this->message->setMessage("Crítica adicionada com sucesso!", "success", "index.php");

        }
        public function update(Movie $movie) {

            $stmt = $this->conn->prepare("UPDATE movies SET
            title = :title,
            description = :description,
            image = :image,
            category = :category,
            trailer = :trailer,
            length = :length
            WHERE id = :id      
          ");

            $stmt->bindParam(":title", $movie->title);
            $stmt->bindParam(":description", $movie->description);
            $stmt->bindParam(":image", $movie->image);
            $stmt->bindParam(":category", $movie->category);
            $stmt->bindParam(":trailer", $movie->trailer);
            $stmt->bindParam(":length", $movie->length);
            $stmt->bindParam(":id", $movie->id);

            $stmt->execute();

            $this->message->setMessage("Filme atualizado com sucesso!", "success", "dashboard.php");

        }
        public function getMoviesReview($id) {

            $reviews = [];

            $stmt = $this->conn->prepare("SELECT * FROM reviews WHERE movies_id = :movies_id");

            $stmt->bindParam(":movies_id", $id);

            $stmt->execute();

            if($stmt->rowCount() > 0 ) {

                $reviewsData = $stmt->fetchAll();

                $userDao = new UserDAO ($this->conn, $this->url);

                foreach($reviewsData as $review) {

                    $reviewObject = $this->buildReview($review);

                    //Chamada de dados do usuário
                    $user = $userDao->findByID($reviewObject->users_id);

                    $reviewObject->user = $user;

                    $reviews[] = $reviewObject;
                }

                return $reviews;

            } else {
                
                return $reviews;
            }

        }
        public function hasAlreadyReviewed($id, $userId) {

            $stmt = $this->conn->prepare("SELECT * FROM reviews WHERE movies_id = :movies_id AND users_id = :users_id");

            $stmt->bindParam(":movies_id", $id);
            $stmt->bindParam(":users_id", $userId);
            $stmt->execute();

            if($stmt->rowCount() > 0) {
                return true;
            } else {
                return false;
            }

        }
        public function getRatings($id) {

            $stmt = $this->conn->prepare("SELECT * FROM reviews WHERE movies_id = :movies_id");
            $stmt->bindParam(":movies_id", $id);
            $stmt->execute();

            if($stmt->rowCount() > 0) {

                $rating = 0;

                $reviews = $stmt->fetchAll();

                foreach($reviews as $review) {
                    $rating += $review["rating"];
                }

                return $rating = $rating / count($reviews);

            } else {

                return $rating = "Não avaliado";
            }

        }

    }