<?php

class LessonRepository
{
    // private PDO $connection;

    // public function __construct(Database $database)
    // {
    //     $this->connection = $database->getConnection();
    // }

    

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
        }
        return $data;
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

    public function update(Lesson $Lesson, Lesson $NewLesson): int
    {
        $oPDO = PDOConnection::get();

        $query = "UPDATE lesson 
                    SET 
                        duration = :duration,
                        date = :date,
                        price = :price,
                        id_category = :id_category
                    WHERE id = :id";
        $statement = $oPDO->prepare($query);
        $statement->bindValue(':duration', $NewLesson->getDuration(), PDO::PARAM_INT);
        $statement->bindValue(':date', $NewLesson->getDate()->format("Y-m-d H:i:s"), PDO::PARAM_STR);
        $statement->bindValue(':price', $NewLesson->getPrice(), PDO::PARAM_INT);
        $statement->bindValue(':id_category', $NewLesson->getIdCategory(), PDO::PARAM_INT);
        $statement->bindValue(':id', $Lesson->getId(), PDO::PARAM_INT);
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