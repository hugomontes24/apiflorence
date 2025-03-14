<?php

class LessonCategoryController
{
    public function __construct( private LessonCategoryRepository $LessonCategoryRepository) {}

    public function processRequest(string $method, ?int $id, ?string $email, ?string $lessons, ?int $user_id): void
    {
        if($id !== null){
            if($lessons == "lessons"){
                $this->processResourceLessonsRequest($method, $id, $user_id);
                return;
            }
            $this->processResourceRequest($method, $id);
            return;
        }
        
        $this->processCollectionRequest($method);
    }

    private function processResourceLessonsRequest(string $method, int $id, ?int $user_id): void
    {
        $a_lessonCategory = $this->LessonCategoryRepository->getOne($id); 
        if(empty($a_lessonCategory)){
            http_response_code(404);
            echo json_encode(['message' => 'Category not found with this id']);
            return;
        }  
        switch($method){
            case 'GET':
                $LessonRepository = new LessonRepository();
                $lessons = $LessonRepository->getAllByIdCategory($id);
                echo json_encode($lessons);
                break;

            default:
                http_response_code(405);
                header("Allow: GET");
            }






    }




    private function processResourceRequest(string $method, int $id): void
    {
        
        $a_lessonCategory = $this->LessonCategoryRepository->getOne($id);
        if($a_lessonCategory === null){
            http_response_code(404);
            return;
        }
        
        $LessonCategory = new LessonCategory();
        $LessonCategory->hydrate($a_lessonCategory);
        switch($method){
            case 'GET':
                echo json_encode($LessonCategory->toArray());
                break;
            case 'PATCH':
                $data = (array) json_decode(file_get_contents('php://input'), true);
                $NewLessonCategory = new LessonCategory();
                $NewLessonCategory->hydrate($data);

                $errors = $this->getValidationErrors($NewLessonCategory);
                if(!empty($errors)){
                    http_response_code(422);
                    echo json_encode($errors);
                    break;
                }
                
                $rows = $this->LessonCategoryRepository->update( $LessonCategory, $NewLessonCategory );
                http_response_code(200);
                echo json_encode([
                    'message' => "LessonCategory id = $id modified",
                    'rows' => $rows
                ]);
                break;

            case 'DELETE':
                $rows = $this->LessonCategoryRepository->delete($id);
                http_response_code(204);
                // echo json_encode([
                //     'message' => "LessonCategory id = $id deleted",
                //     'rows' => $rows
                // ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, PATCH, DELETE");
        }
    }

    private function processCollectionRequest(string $method): void
    {
        switch($method){
            case 'GET':
                echo json_encode($this->LessonCategoryRepository->getAll());
                break;
            case 'POST':
                $data = (array) json_decode(file_get_contents('php://input'),true);
                $NewLessonCategory = new LessonCategory();
                $NewLessonCategory->hydrate($data);

                $errors = $this->getValidationErrors($NewLessonCategory);
                if(!empty($errors)){
                    http_response_code(422);
                    echo json_encode($errors);
                    break;
                }

                $id = $this->LessonCategoryRepository->create($NewLessonCategory);  
                http_response_code(201);
                echo json_encode([
                    'message' => "LessonCategory id = $id created",
                    'id' => $id
                ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }

    private function getValidationErrors(LessonCategory $LessonCategory): array
    {
        $errors = [];
        if($LessonCategory->getName() === ''){
            $errors['name'] = "Name is required";
        }
        
    
        return $errors;
    }   

}
