<?php

class LessonUser {

    public const DB_TABLE = 'lesson_user';
    
    private int $id;
    private int $lesson_id;
    private int $user_id;
    private bool $is_paid = false;

    public function __construct(?int $lessonId = null, ?int $userId = null, ?bool $isPaid = false) {
        $this->lesson_id = $lessonId ?? -1;
        $this->user_id = $userId ?? -1;
        $this->is_paid = $isPaid;
    }

    public function hydrate(array $data): self
    {
        $this->setId($data['id'] ?? -1);
        $this->setLessonId($data['lesson_id'] ?? -1);
        $this->setUserId($data['user_id'] ?? -1);
        $this->setIsPaid($data['is_paid'] ?? false);

        return $this;
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'lesson_id' => $this->lesson_id,
            'user_id' => $this->user_id,
            'is_paid' => $this->is_paid,
        ];
    }

    public function getId(): int {
        return $this->id;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function getLessonId(): int 
    {
        return $this->lesson_id;
    }

    public function setLessonId(int $lesson_id): void 
    {
        $this->lesson_id = $lesson_id;
    }   
    public function getUserId(): int 
    {
        return $this->user_id;
    }
    public function setUserId(int $user_id): void 
    {
        $this->user_id = $user_id;
    }
    public function getIsPaid(): bool 
    {
        return $this->is_paid;
    }
    public function setIsPaid(bool $is_paid): void 
    {
        $this->is_paid = $is_paid;
    }


    // public function lesson() {
    //     return $this->belongsTo('Lesson');
    // }

    // public function user() {
    //     return $this->belongsTo('Course');
    // }
}