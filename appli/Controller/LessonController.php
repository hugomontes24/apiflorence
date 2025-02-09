<?php
 class LessonController 
{
    public function __construct( private LessonRepository $LessonRepository){}

    public function processRequest( string $method, ?string $id) :void  // le point d'interrogation means nullable
    {
        if($id){  // single resource
            $this->processResourceRequest($method,$id);

        } else { // collection
            $this->processCollectionRequest($method);
        }
    }

    public function processResourceRequest(string $method, string $id): void    
    {
        $lesson = $this->LessonRepository->getOne($id);

        if(!$lesson){
            http_response_code(404);
            echo json_encode(['message' => 'Session not found']);
            return;
        }

        switch($method){
            case 'GET':
                echo json_encode($lesson);
                break;
            case 'PATCH':
                $data = (array) json_decode(file_get_contents('php://input'), true);

                $errors = $this->getValidationErrors($data);
                if(!empty($errors)){
                    http_response_code(422);
                    echo json_encode($errors);
                    break;
                }
                
                $rows = $this->LessonRepository->update( $lesson, $data );
                http_response_code(200);
                echo json_encode([
                    'message' => "Session id = $id modified",
                    'rows' => $rows
                ]);
                break;

            case 'DELETE':
                // $rows = $this->SessionRepository->delete($id);
                // http_response_code(204);
                // echo json_encode([
                //     'message' => "Session id = $id deleted",
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
    
    private function getValidationErrors(array $data): array
    {
        $errors = [];
        if(!isset($data['name']) || empty($data['name'])){
            $errors['name'] = 'Name is required';
        }
        if(!isset($data['date']) || empty($data['date'])){
            $errors['date'] = 'Date is required';
        }
        if(!isset($data['date']) || empty($data['date']) || !$this->isDateTimeValid($data['date'])){
            $errors['date'] = 'Date is not valid';
        }
        if( isset($data['price'])  &&  filter_var($data['price'], FILTER_VALIDATE_INT) === false){
            $errors['price'] = 'Price must be a number';
        }
        
        return $errors;
    }

    function isDateTimeValid($dateTime): bool
    {
        $DateObject = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
        return ($DateObject instanceof DateTime);        
    }
}