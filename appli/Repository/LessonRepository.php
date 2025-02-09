<?php

class LessonRepository
{
    private PDO $connection;

    public function __construct(Database $database)
    {
        $this->connection = $database->getConnection();
    }

    public function getOne(string $id): array | false
    {
        $query = "SELECT * FROM lesson WHERE id = :id";
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        if(!$data){
            throw new Exception("Lesson not found", 404);
        }
        //$data["is_valid"] = (bool)$data["is_valid"];
        return $data;
    }

    public function getAll(): array
    {
        $query = "SELECT * FROM lesson";
        $statement = $this->connection->query($query);
        $data = [];
        while($row = $statement->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
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

    public function update(array $current, array $new): int
    {
        $query = "UPDATE lesson 
                    SET 
                        name = :name,
                        duration = :duration,
                        date = :date,
                        price = :price
                    WHERE id = :id";
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':name', $new['name'], PDO::PARAM_STR);
        $statement->bindValue(':duration', $new['duration'], PDO::PARAM_INT);
        $statement->bindValue(':date', $new['date'], PDO::PARAM_STR);
        $statement->bindValue(':price', $new['price'], PDO::PARAM_INT);
        $statement->bindValue(':id', $current['id'], PDO::PARAM_INT);
        $statement->execute();
        return $statement->rowCount();
    }
}