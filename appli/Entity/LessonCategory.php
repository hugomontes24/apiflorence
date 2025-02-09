<?php
class LessonCategory
{
    public const DB_TABLE = 'lesson_category';

    private ?int $id = null;
    private string $name;
    private string $description;
    

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
        ];
    }

    public function hydrate(array $data): self
    {
        (isset ($data['id'])) ?$this->setId( intval($data['id'])): $this->setId(-1);
        (isset ($data['name'])) ?$this->setName( $data['name']): $this->setName('');
        (isset ($data['description'])) ?$this->setDescription( $data['description']): $this->setDescription('');
        return $this;
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

    public function getDescription(): string
    {
        return $this->description;
    }
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}