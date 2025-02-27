<?php

class LessonMapper
{
    public function dataToGetDTOArray($data): array
    {
        $lessons = [];    
        foreach ($data as $lesson) {
            $lessons[] = $this->dataToGetDTO($lesson);    
        }
        return $lessons;    
    }

    public function dataToGetDTO($data): LessonGetDTO
    {
        $LessonGetDTO = new LessonGetDTO();
        $LessonGetDTO->hydrate($data);
        return $LessonGetDTO;                   
    }
}