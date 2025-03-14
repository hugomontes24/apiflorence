<?php
 class LessonController 
{
    public function __construct( 
        private LessonRepository $LessonRepository,
        private LessonMapper $LessonMapper
        ){}

    public function processRequest( string $method, ?int $id, ?string $email, ?string $reservations, ?int $user_id) :void  // le point d'interrogation means nullable
    {
        if($id){  // single resource
            if($reservations == "reservations"){
                $this->processResourceReservationRequest($method, $id, $user_id);
                return;
            }
            $this->processResourceRequest($method,$id);
            return;
        }
        // collection
        $this->processCollectionRequest($method);  
    }

    public function processResourceReservationRequest(string $method, string $id, ?int $user_id): void    
    {
        // 1 verifier que la lesson existe
        $a_lesson = $this->LessonRepository->getOne($id);
        if(empty($a_lesson)){
            http_response_code(404);
            echo json_encode(['message' => 'Lesson not found with this id']);
            return;
        }
        $LessonUserRepository = new LessonUserRepository();
        $Lesson = new Lesson(); // TODO à enlever ou retravailler
        $Lesson->hydrate($a_lesson);
        switch($method){
            case 'POST':
                $data = (array) json_decode(file_get_contents('php://input'), true);
                 // 2 verifier que le user existe
                 $UserRepository = new UserRepository();
                 $a_user = $UserRepository->getOneByEmail($data['email']);
                 if(empty($a_user)){
                     // 3 else creer l user et recuperer id
                    $User = new User();
                    $User->hydrate($data);
                    $errors = $this->getValidationErrorsUser($User);
                    if(!empty($errors)){
                        http_response_code(422);
                        echo json_encode($errors);
                        break;    
                    }   
                    //$User->setPassword(password_hash($User->getPassword(), PASSWORD_DEFAULT));
                     $a_user['id'] = $UserRepository->create($User);                
                 }
                // 4 ajouter l user a la lesson
                $LessonUser = new LessonUser($id, $a_user['id'], false);
                $lessonUserId = $LessonUserRepository->create($LessonUser);
                
                http_response_code(201);
                echo json_encode($a_user); // envoyer l user pour rafraichir les reservations
                break;

            case 'DELETE':
                $LessonUserRepository->deleteWithIds($id, $user_id);
                http_response_code(204);
                break;

            default:
                http_response_code(405);
                header("Allow: POST, DELETE");
            }   
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
                
                $rows = $this->LessonRepository->update( $Lesson->getId(), $NewLesson );
                http_response_code(200);
                echo json_encode([
                    'message' => "Lesson id = $id modified",
                    'rows' => $rows
                ]);
                break;

            case 'DELETE':
                $this->LessonRepository->delete($id);
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
                break;
            case 'POST':
                $data = (array) json_decode(file_get_contents('php://input'), true);
                
                $Lesson = new Lesson(); // TODO à enlever ou retravailler
                $Lesson->hydrate($data);

                $errors = $this->getValidationErrors($Lesson);
                if(!empty($errors)){
                    http_response_code(422);
                    echo json_encode($errors);
                    break;
                }

                $id = $this->LessonRepository->create($Lesson);
                http_response_code(201);
                echo json_encode([
                    'message' => "Lesson created",
                    'id' => $id
                ]);
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

    private function getValidationErrorsUser(User $user): array
    {
        $errors = [];
        if(null === $user->getName() || $user->getName() === ''){
            $errors['name'] = 'Name is required';
        }
        if(null === $user->getEmail() || $user->getEmail() === ''){
            $errors['email'] = 'Email is required';
        }
        if(null === $user->getAge() || $user->getAge() === ''){
            $errors['age'] = 'Age is required';
        }
        if(null !== $user->getAge() && filter_var( $user->getAge() , FILTER_VALIDATE_INT) === false){
            $errors['age'] = 'Age must be a number';
        }  
        return $errors;
    }

    function isDateTimeValid($dateTime): bool
    {
        $DateObject = DateTime::createFromFormat('Y-m-d H:i:s', $dateTime);
        return ($DateObject instanceof DateTime);        
    }
}