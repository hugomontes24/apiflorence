<?php

class LessonRepository
{
    public function create(Lesson $Lesson): string
    {
        $oPDO = PDOConnection::get();

        $query = "INSERT INTO lesson 
                        (date, duration, price, nb_max_users, id_category) 
                    VALUES 
                        (:date, :duration, :price, :nbMaxUsers, :idCategory)";
        $statement = $oPDO->prepare($query);
        $statement->bindValue(':date', $Lesson->getDate()->format("Y-m-d H:i:s"), PDO::PARAM_STR);
        $statement->bindValue(':duration', $Lesson->getDuration(), PDO::PARAM_INT);
        $statement->bindValue(':price', $Lesson->getPrice(), PDO::PARAM_INT);
        $statement->bindValue(':nbMaxUsers', $Lesson->getNbMaxUsers(), PDO::PARAM_INT);
        $statement->bindValue(':idCategory', $Lesson->getIdCategory(), PDO::PARAM_INT);
        $statement->execute();
        return $oPDO->lastInsertId();
    }

    public function getOne(string $id): array
    {
        $oPDO = PDOConnection::get();
        
        $query = "SELECT * FROM lesson WHERE id = :id";
        $statement = $oPDO->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        if(!$data){
            $data = [];
            return $data;
        }

        // retrouver tous les users associés à la lesson
        $query = "SELECT * FROM lesson_user WHERE lesson_id = :id";
        $statement = $oPDO->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        
        while($row = $statement->fetch(PDO::FETCH_ASSOC)){ 
            $userRepository = new UserRepository();
            $User = $userRepository->getOne($row["user_id"]); 
            if($User !== null){
                $data["users"][] = $User;
            }
        }
        return $data;
    }

    public function getAll(): array
    {
        $oPDO = PDOConnection::get();

        $query = "SELECT * FROM lesson";
        $statement = $oPDO->query($query);
        $data = [];
        while($row = $statement->fetch(PDO::FETCH_ASSOC)){
            $Lesson = new LessonGetDTO();
            $Lesson->hydrate($row);
            
            $data[] = $Lesson->toArray();
        }
        return $data;
    }

    public function getAllByIdCategory(int $idCategory): array
    {
        $oPDO = PDOConnection::get();

        $query = "SELECT * FROM lesson
            WHERE id_category = :idCategory";

        $statement = $oPDO->prepare($query);
        $statement->bindValue(':idCategory', $idCategory, PDO::PARAM_INT);
        $statement->execute();

        $data = [];
        $userRepository = new UserRepository();
        while($row = $statement->fetch(PDO::FETCH_ASSOC)){
            if($row === false){
                break;
            }
             // retrouver tous les users associés à la lesson
            $queryUsers = "SELECT * FROM lesson_user WHERE lesson_id = :id";
            $statementUser = $oPDO->prepare($queryUsers);
            $statementUser->bindValue(':id', $row["id"], PDO::PARAM_INT);
            $statementUser->execute();
            
            while($row1 = $statementUser->fetch(PDO::FETCH_ASSOC)){ 
                $User = $userRepository->getOne($row1["user_id"]); 
                if($User !== null){
                    $row["users"][] = $User;
                }
            }
            $Lesson = new LessonGetDTO();
            $Lesson->hydrate($row);
            
            $data[] = $Lesson->toArray();
        }
        return $data;
    }

  
    public function update(int $lessonId, Lesson $NewLesson): int
    {
        $oPDO = PDOConnection::get();

        $query = "UPDATE lesson 
                    SET 
                        duration = :duration,
                        date = :date,
                        price = :price,
                        nb_max_users = :nbMaxUsers,
                        id_category = :idCategory
                    WHERE id = :id";
        $statement = $oPDO->prepare($query);
        $statement->bindValue(':duration', $NewLesson->getDuration(), PDO::PARAM_INT);
        $statement->bindValue(':date', $NewLesson->getDate()->format("Y-m-d H:i:s"), PDO::PARAM_STR);
        $statement->bindValue(':price', $NewLesson->getPrice(), PDO::PARAM_INT);
        $statement->bindValue(':nbMaxUsers', $NewLesson->getNbMaxUsers(), PDO::PARAM_INT);
        $statement->bindValue(':idCategory', $NewLesson->getIdCategory(), PDO::PARAM_INT);
        $statement->bindValue(':id', $lessonId, PDO::PARAM_INT);
        $statement->execute();
        return $statement->rowCount();
    }

    public function delete(string $id): int
    {
        $oPDO = PDOConnection::get();

        $query = "DELETE FROM lesson WHERE id = :id";
        $statement = $oPDO->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        
        return $statement->rowCount();
    }


}


 // public function verifyOne(string $id): array
    // {
    //     $oPDO = PDOConnection::get();

    //     $query = "SELECT * FROM lesson WHERE id = :id";
    //     $statement = $oPDO->prepare($query);
    //     $statement->bindValue(':id', $id, PDO::PARAM_INT);
    //     $statement->execute();
    //     $data = $statement->fetch(PDO::FETCH_ASSOC);
    //     if($data === false){
    //         $data = [];
    //     }
    //     //$data["is_valid"] = (bool)$data["is_valid"];
    //     return $data;
    // }

      // public function create(array $data): string
    // {
    //     $query = "INSERT INTO session 
    //                     (name, age, is_valid) 
    //                 VALUES 
    //                     (:name, :age, :is_valid)";
    //     $statement = $this->connection->prepare($query);

    //     $statement->bindValue(':name', $data['name'], PDO::PARAM_STR);
    //     $statement->bindValue(':age', $data['age'], PDO::PARAM_INT);
    //     $statement->bindValue(':is_valid', (bool) $data['is_valid'] ?? false, PDO::PARAM_BOOL);
    //     $statement->execute();

    //     return $this->connection->lastInsertId();
    // }


     // private PDO $connection;

    // public function __construct(Database $database)
    // {
    //     $this->connection = $database->getConnection();
    // }
