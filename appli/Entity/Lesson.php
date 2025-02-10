<?php
class Lesson
{
    public const DB_TABLE = 'lesson';

    private ?int $id = null;
    private ?DateTime $date= null; 
    private int $duration; // en minutes
    private int $price;
    private int $id_category;
    private array $users = []; // tableau d'objets User, les élèves inscrits à ce cours
    

    public function hydrate(array $data): self
    {
        if(isset($data['date'])){
            $DateObject = DateTime::createFromFormat('Y-m-d H:i:s', $data['date']);
            ($DateObject instanceof DateTime) ?$this->setDate($DateObject): $this->setDate(null);
        }

        (isset ($data['id'])) ?$this->setId( intval($data['id'])): $this->setId(-1);
        (isset ($data['duration'])) ?$this->setDuration( intval($data['duration'])): $this->setDuration(-1);
        (isset ($data['price'])) ?$this->setPrice(intval( $data['price'])): $this->setPrice(0);
        (isset ($data['id_category'])) ?$this->setIdCategory( intval($data['id_category'])): $this->setIdCategory(-1);
        (isset ($data['users'])) ?$this->setUsers($data['users']): $this->setUsers([]);
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date,
            'duration' => $this->duration,
            'price' => $this->price,
            'id_category' => $this->id_category,
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


    public function getPrice(): string
    {
        return $this->price;
    }
    public function setPrice(string $price): void
    {
        $this->price = $price;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }
    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    public function getIdCategory(): int
    {
        return $this->id_category;
    }
    public function setIdCategory(int $id_category): void
    {
        $this->id_category = $id_category;
    }
    public function getDate(): DateTime
    {
        return $this->date;
    }
    public function setDate(?DateTime $date): void
    {
        $this->date = $date;
    }  
    public function getUsers(): array
    {
        return $this->users;
    }
    public function setUsers(array $users): void
    {
        $this->users = $users;
    }                             

}