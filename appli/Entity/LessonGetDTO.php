<?php
class LessonGetDTO
{
    public const DB_TABLE = 'lesson';
    private ?int $id = null;
    private ?DateTime $date= null; 
    private int $duration; // en minutes
    private int $price;
    private int $nbMaxUsers;
    private int $idCategory;
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
        (isset ($data['nb_max_users'])) ? $this->setNbMaxUsers($data['nb_max_users']) : $this->setNbMaxUsers(0);
        (isset ($data['id_category'])) ? $this->setIdCategory($data['id_category']) : $this->setIdCategory(-1); 
        (isset ($data['users'])) ?$this->setUsers($data['users']): $this->setUsers([]);

        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'date' => $this->getDate(),
            'duration' => $this->getDuration(),
            'price' => $this->getPrice(),
            'nbMaxUsers' => $this->getNbMaxUsers(),
            'idCategory' => $this->getIdCategory(),
            'users' => $this->getUsers()
        ];
    }

    public function getUsers(): array
    {
        return $this->users;
    }
    public function setUsers(array $users): void
    {
        $this->users = $users;
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
    public function getIdCategory(): int
    {
        return $this->idCategory;
    }
    public function setIdCategory(?int $idCategory): void
    {
        $this->idCategory = $idCategory;
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
}