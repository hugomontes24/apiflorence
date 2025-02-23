<?php
class LessonDTO
{
    public const DB_TABLE = 'lesson';
    private ?int $id = null;
    private ?DateTime $date= null; 
    private int $duration; // en minutes
    private int $price;
    private int $nbMaxUsers;
    private ?LessonCategory $lessonCategory= null;
    

    public function hydrate(array $data): self
    {
        if(isset($data['date'])){
            $DateObject = DateTime::createFromFormat('Y-m-d H:i:s', $data['date']);
            ($DateObject instanceof DateTime) ?$this->setDate($DateObject): $this->setDate(null);
        }

        (isset ($data['id'])) ?$this->setId( intval($data['id'])): $this->setId(-1);
        (isset ($data['duration'])) ?$this->setDuration( intval($data['duration'])): $this->setDuration(-1);
        (isset ($data['price'])) ?$this->setPrice(intval( $data['price'])): $this->setPrice(0);
        (isset ($data['nb_max_users'])) ? $this->setNbMaxUsers($data['nb_max_users']) : $this->setNbMaxUsers(0);
        
        $LessonRepository = new LessonRepository(new Database('localhost', DB_BASE, DB_USER, DB_PASS));
        $data1 = $LessonRepository->getOne($data['id_category']);
        $LessonCategory = new LessonCategory();
        $LessonCategory->hydrate($data1);
        (isset ($data['id_category'])) ?$this->setLessonCategory($LessonCategory): $this->setLessonCategory(null);
        
        
        return $this;
    }

    public function hydrateFromObject(Lesson $Lesson): self
    {
        var_dump($Lesson);
        ($Lesson->getId() !== null) ?$this->setId( $Lesson->getId()): $this->setId(-1); 
        ( $Lesson->getDate()!== null ) ?$this->setDate($Lesson->getDate()): $this->setDate(null);
        ( $Lesson->getDuration()!== null ) ?$this->setDuration($Lesson->getDuration()): $this->setDuration(-1);
        ( $Lesson->getPrice()!== null ) ?$this->setPrice($Lesson->getPrice()): $this->setPrice(0);

        if($Lesson->getIdCategory() !== null){
            $LessonCategoryRepository = new LessonCategoryRepository(new Database('localhost', DB_BASE, DB_USER, DB_PASS));
            $data = $LessonCategoryRepository->getOne($Lesson->getIdCategory());
            if(!empty($data)){
                $LessonCategory = new LessonCategory();
                $LessonCategory->hydrate($data);
                $this->setLessonCategory($LessonCategory);            
            }
        }else{
            $this->setLessonCategory(null);
        }
        
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'duration' => $this->duration,
            'price' => $this->price,
            'lessonCategory' => $this->lessonCategory->toArray(),
        ];
    }

    public function getId(): int
    {
        return $this->id;
    } 
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function getDate(): DateTime
    {
        return $this->date;
    }public function setDate(?DateTime $date): void
    {
        $this->date = $date;
    }
    public function getDuration(): int
    {
        return $this->duration;
    }
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }
    public function getPrice(): int
    {
        return $this->price;
    }
    public function setPrice(int $price): void
    {
        $this->price = $price;
    }
    public function getNbMaxUsers(): int
    {
        return $this->nbMaxUsers;
    }

    public function setNbMaxUsers(int $nbMaxUsers): self
    {
        $this->nbMaxUsers = $nbMaxUsers;

        return $this;
    }
    public function getLessonCategory(): LessonCategory
    {
        return $this->lessonCategory;
    }
    public function setLessonCategory(?LessonCategory $lessonCategory): void
    {
        $this->lessonCategory = $lessonCategory;
    }
}