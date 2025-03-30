<?php
class UserGetDTO
{
    public const DB_TABLE = 'user';

    private ?int $id = null;
    private string $name;
    private string $email;
    private int $age;
    private bool $is_valid;
    private int $reservation_id = -1;

    // public function __construct(array $data)
    // {
    //     $this->id = $data['id'];
    //     $this->name = $data['name'];
    //     $this->age = $data['age'];
    //     $this->is_valid = $data['is_valid'];
    // }

    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'age' => $this->age,
            'is_valid' => $this->is_valid,
            'reservation_id' => $this->reservation_id
        ];
    }

    public function hydrate(array $data): self
    {
        (isset ($data['id'])) ?$this->setId( intval($data['id'])): $this->setId(-1);
        (isset ($data['name'])) ?$this->setName( $data['name']): $this->setName('');
        (isset ($data['email'])) ?$this->setEmail( $data['email']): $this->setEmail('');
        (isset ($data['age'])) ?$this->setAge( intval($data['age'])): $this->setAge(-1);
        (isset ($data['is_valid'])) ?$this->setIsValid( boolval($data['is_valid'])): $this->setIsValid(false);
        (isset ($data['reservation_id'])) ?$this->setReservationId( intval($data['reservation_id'])): $this->setReservationId(-1);
        return $this;
    }
    public function getReservationId(): int
    {
        return $this->reservation_id;
    }
    public function setReservationId(int $reservation_id): void
    {
        $this->reservation_id = $reservation_id;
    }

    public function getId(): int
    {
        return $this->id;
    } 
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function setName(string $name): void
    {
        $this->name = $name;
    }
    
    public function getEmail(): string
    {
        return $this->email;
    }
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getAge(): int
    {
        return $this->age;
    }
    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    public function getIsValid(): bool
    {
        return $this->is_valid;
    }
    public function setIsValid(bool $is_valid): void
    {
        $this->is_valid = $is_valid;
    }

}