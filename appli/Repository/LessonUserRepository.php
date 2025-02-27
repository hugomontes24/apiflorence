<?php

class LessonUserRepository
{  
    public function create(LessonUser $LessonUser): int
    {
        $oPDO = PDOConnection::get();

        $sql = "INSERT INTO lesson_user 
                    (lesson_id, user_id, is_paid) 
                VALUES (:lesson_id, :user_id, :is_paid)";
        $stmt = $oPDO->prepare($sql);
        $stmt->bindValue(':lesson_id', $LessonUser->getLessonId(), PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $LessonUser->getUserId(), PDO::PARAM_INT);
        $stmt->bindValue(':is_paid', $LessonUser->getIsPaid(), PDO::PARAM_BOOL);
        $stmt->execute();
        return $oPDO->lastInsertId();
    }

    public function getAll(): array
    {
        $oPDO = PDOConnection::get();

        $sql = "SELECT * FROM lesson_user";
        $stmt = $oPDO->query($sql);
        $data=[];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row;
        }

        return $data;
    }

    public function getOne(int $id): array
    {
        $oPDO = PDOConnection::get();

        $sql = "SELECT * FROM lesson_user WHERE id = :id";
        $stmt = $oPDO->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        var_dump($data);
        if(!$data){
            return $data = [];
        }
        return $data;
    }

    public function delete(int $id): int
    {
        $oPDO = PDOConnection::get();

        $query = "DELETE FROM lesson_user WHERE id = :id";
        $statement = $oPDO->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->rowCount();
    }
    public function deleteWithIds(int $lesson_id, int $user_id): int
    {
        $oPDO = PDOConnection::get();

        $query = "DELETE FROM lesson_user 
                WHERE lesson_id = :lesson_id 
                AND   user_id = :user_id 
                LIMIT 1";
        $statement = $oPDO->prepare($query);
        $statement->bindValue(':lesson_id', $lesson_id, PDO::PARAM_INT);
        $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $statement->execute();

        return $statement->rowCount();
    }

    public function registeredUsers(int $lesson_id): array
    {
        $oPDO = PDOConnection::get();

        $sql = "SELECT * FROM lesson_user WHERE id = :id";
        $sql = "SELECT u.id 
                from user as u join 
                lesson_user as lu on u.id = lu.user_id 
                where lu.lesson_id = :id";
        $stmt = $oPDO->prepare($sql);
        $stmt->bindValue(':id', $lesson_id, PDO::PARAM_INT);
        $stmt->execute();
        $data=[];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $data[] = $row["id"];
        }
        return $data;
    }


    public function countUsersByLessonId(int $lesson_id): int
    {
        $oPDO = PDOConnection::get();

        $sql = "SELECT COUNT(*) FROM lesson_user WHERE lesson_id = :lesson_id";
        $stmt = $oPDO->prepare($sql);
        $stmt->bindValue(':lesson_id', $lesson_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
  
}


 
    // private PDO $connection;   

    // public function __construct(Database $database)
    // {
    //     $this->connection = $database->getConnection();
    // }