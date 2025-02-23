<?php
 class LessonController 
{
    public function __construct( private LessonRepository $LessonRepository){}

    public function processRequest( string $method, ?int $id) :void  // le point d'interrogation means nullable
    {
        if($id){  // single resource
            $this->processResourceRequest($method,$id);
            return;
        }
        // collection
        $this->processCollectionRequest($method);  
    }

    public function processResourceRequest(string $method, string $id): void    
    {
        $a_lesson = $this->LessonRepository->getOne($id);

        if(empty($a_lesson)){
            http_response_code(404);
            echo json_encode(['message' => 'Session not found with this id']);
            return;
        }

        $Lesson = new Lesson(); // TODO à enlever ou retravailler
        $Lesson->hydrate($a_lesson);
        switch($method){
            case 'GET':
                $LessonGetDTO = new LessonGetDTO();
                $LessonGetDTO->hydrate($a_lesson);
                echo json_encode($LessonGetDTO->toArray());
                break;
            case 'PATCH':
                $data = (array) json_decode(file_get_contents('php://input'), true);
                $NewLesson = new Lesson();
                $NewLesson->hydrate($data);

                $errors = $this->getValidationErrors($NewLesson);
                if(!empty($errors)){
                    http_response_code(422);
                    echo json_encode($errors);
                    break;
                }
                
                $rows = $this->LessonRepository->update( $Lesson, $NewLesson );
                http_response_code(200);
                echo json_encode([
                    'message' => "Lesson id = $id modified",
                    'rows' => $rows
                ]);
                break;

            case 'DELETE':
                $rows = $this->LessonRepository->delete($id);
                http_response_code(204);
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
                $lessons = $this->LessonRepository->getAll();
                echo json_encode($lessons);
                // $sessions = $this->SessionRepository->getAll();
                // echo json_encode($sessions);
                break;
            case 'POST':
                // $data = (array) json_decode(file_get_contents('php://input'), true);

                // $errors = $this->getValidationErrors($data);
                // if(!empty($errors)){
                //     http_response_code(422);
                //     echo json_encode($errors);
                //     break;
                // }

                // $id = $this->SessionRepository->create($data);
                // http_response_code(201);
                // echo json_encode([
                //     'message' => "Session created",
                //     'id' => $id
                // ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }
    
    private function getValidationErrors(Lesson $Lesson): array
    {
        $errors = [];
        if($Lesson->getDate()===null){
            $errors['date'] = 'Date is required';
        }
       if($Lesson->getDuration()===-1){
            $errors['duration'] = 'Duration is required';
        }
        if($Lesson->getPrice()===-1){
            $errors['price'] = 'Price is required';
        }

        return $errors;
    }

    function isDateTimeValid($dateTime): bool
    {
        $DateObject = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
        return ($DateObject instanceof DateTime);        
    }
}