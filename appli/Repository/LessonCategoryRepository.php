<?php
class LessonCategoryRepository
{

    public function getAll(): array
    {
        $oPDO = PDOConnection::get();

        $sql = "SELECT * FROM lesson_category";
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

        $sql = "SELECT * FROM lesson_category WHERE id = :id";
        $stmt = $oPDO->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$data){
            throw new Exception("LessonCategory not found", 404);
        }
        return $data;
        
    }

    public function create(LessonCategory $LessonCategory): int
    {
        $oPDO = PDOConnection::get();

        $sql = "INSERT INTO lesson_category 
                    (name, description) 
                VALUES (:name, :description)";
        $stmt = $oPDO->prepare($sql);
        $stmt->bindValue(':name', $LessonCategory->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':description', $LessonCategory->getDescription(), PDO::PARAM_STR);
        $stmt->execute();
        return $oPDO->lastInsertId();
    }

    public function update(LessonCategory $LessonCategory, LessonCategory $NewLessonCategory): int
    {
        $oPDO = PDOConnection::get();

        $sql = "UPDATE lesson_category 
                SET 
                    name = :name, 
                    description = :description 
                WHERE id = :id";
        $stmt = $oPDO->prepare($sql);
        $stmt->bindValue(':name', $NewLessonCategory->getName(), PDO::PARAM_STR);
        $stmt->bindValue(':description', $NewLessonCategory->getDescription(), PDO::PARAM_STR);
        $stmt->bindValue(':id', $LessonCategory->getId(), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function delete(int $id): int
    {
        $oPDO = PDOConnection::get();
        $sql = "DELETE FROM lesson_category WHERE id = :id";
        $stmt = $oPDO->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount();
    }
}