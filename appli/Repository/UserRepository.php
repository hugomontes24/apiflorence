<?php

class UserRepository
{
    private PDO $connection;

    public function __construct(Database $database)
    {
        $this->connection = $database->getConnection();
    }

    public function getAll(): array
    {
        $query = "SELECT * FROM user";
        $statement = $this->connection->query($query);
        $data = [];
        while($row = $statement->fetch(PDO::FETCH_ASSOC)){

            $row["is_valid"] = (bool)$row["is_valid"];
            $data[] = $row;
        }
        return $data;
    }

    public function create(User $NewUser): int
    {
        $query = "INSERT INTO user 
                        (name, age, is_valid) 
                    VALUES 
                        (:name, :age, :is_valid)";
        $statement = $this->connection->prepare($query);

        $statement->bindValue(':name', $NewUser->getName(), PDO::PARAM_STR);
        $statement->bindValue(':age', $NewUser->getAge(), PDO::PARAM_INT);
        $statement->bindValue(':is_valid', (bool) $NewUser->getIsValid() ?? false, PDO::PARAM_BOOL);
        // $statement->bindValue(':name', $data['name'], PDO::PARAM_STR);  // requete avec le tableau $data
        // $statement->bindValue(':age', $data['age'], PDO::PARAM_INT);
        // $statement->bindValue(':is_valid', (bool) $data['is_valid'] ?? false, PDO::PARAM_BOOL);

        $statement->execute();

        return $this->connection->lastInsertId();
    }

    public function getOne(string $id): array | false
    {
        $query = "SELECT * FROM user WHERE id = :id";
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();
        $data = $statement->fetch(PDO::FETCH_ASSOC);
        if(!$data){
            throw new Exception("User not found", 404);
        }
        $data["is_valid"] = (bool)$data["is_valid"];
        return $data;
    }

    public function update(User $User, User $NewUser): int
    {
        $query = "UPDATE user 
                    SET 
                        name = :name, 
                        age = :age, 
                        is_valid = :is_valid
                    WHERE id = :id";
        $statement = $this->connection->prepare($query);

        $statement->bindValue(':name', $NewUser->getName(), PDO::PARAM_STR);
        $statement->bindValue(':age', $NewUser->getAge(), PDO::PARAM_INT);
        $statement->bindValue(':is_valid', (bool) $NewUser->getIsValid() ?? false, PDO::PARAM_BOOL);
        $statement->bindValue(':id', $User->getId(), PDO::PARAM_INT);

        $statement->execute();

        return $statement->rowCount();
    }

    // public function update(array $current, array $new): int
    // {
    //     $query = "UPDATE user 
    //                 SET 
    //                     name = :name, 
    //                     age = :age, 
    //                     is_valid = :is_valid
    //                 WHERE id = :id";
    //     $statement = $this->connection->prepare($query);

    //     $statement->bindValue(':name', $new['name'], PDO::PARAM_STR);
    //     $statement->bindValue(':age', $new['age'], PDO::PARAM_INT);
    //     $statement->bindValue(':is_valid', (bool) $new['is_valid'] ?? false, PDO::PARAM_BOOL);
    //     $statement->bindValue(':id', $current['id'], PDO::PARAM_INT);

    //     $statement->execute();

    //     return $statement->rowCount();
    // }

    public function delete(string $id): int
    {
        $query = "DELETE FROM user WHERE id = :id";
        $statement = $this->connection->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->rowCount();
    }





}