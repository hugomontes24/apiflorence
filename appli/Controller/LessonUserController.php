<?php
class LessonUserController 
{
    public function __construct(
        private LessonUserRepository $LessonUserRepository
    ){}

    public function processRequest(string $method, ?int $id, ?string $email, ?string $reservation, ?int $user_id): void
    {
        if($id !== null){
            $this->processResourceRequest($method, $id);
            return;
        }
        
        $this->processCollectionRequest($method);
    }

    private function processResourceRequest(string $method, int $id)
    {
        $a_lessonUser = $this->LessonUserRepository->getOne($id);
        if(empty($a_lessonUser )){
            http_response_code(404);
            echo json_encode(['message' => 'Lesson->user not found']);
            return;
        }
        
        $LessonUser = new LessonUser();
        $LessonUser->hydrate($a_lessonUser);
        switch($method){
            case 'GET':
                echo json_encode($LessonUser->toArray());
                break;

            case 'DELETE':
                $this->LessonUserRepository->delete($id);
                http_response_code(204);
                // echo json_encode([
                //     'message' => "LessonUser id = $id deleted",
                //     'rows' => $rows
                // ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, DELETE");
                break;
        }
    }

    private function processCollectionRequest(string $method): void
    {
        switch($method){
            case 'GET':
                echo json_encode($this->LessonUserRepository->getAll());
                break;
            case 'POST':
                $data = (array) json_decode(file_get_contents('php://input'), true);
                $LessonUser = new LessonUser();
                $LessonUser->hydrate($data);
                
                $errors = $this->getValidationErrors($LessonUser,$this->LessonUserRepository, $method);
                if(!empty($errors)){
                    http_response_code(422);
                    echo json_encode($errors);
                    break;
                }
                
                $id = $this->LessonUserRepository->create($LessonUser);
                http_response_code(201);
                echo json_encode([
                        'message' => "LessonUser created",
                        'id' => $id
                    ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST");
                break;
        }

    }
    
    private function getValidationErrors(LessonUser $LessonUser, LessonUserRepository $LessonUserRepository, string $method): array
    {
        if($method == 'POST'){
            $errors = [];
            if($LessonUser->getLessonId() <= 0){
                $errors['lesson_id'] = "Lesson id is required";
            }
            if($LessonUser->getUserId() <= 0 ){
                $errors['user_id'] = "User id is required";
            }

            if($this->verifyUserId($LessonUser->getUserId()) === false){
                $errors['user'] = "User with this id doesn't exist";
            }

            if($this->verifyLessonId($LessonUser->getLessonId()) !== false){
                if($this->checkAvalaibility($LessonUser->getLessonId(), $LessonUserRepository) === false){
                    $errors['lesson_verify_max_users'] = "Lesson is full";
                }
                if($this->verifyUnicityLessonUser($LessonUser->getLessonId(), $LessonUser->getUserId()) === false){
                    $errors['lesson_verify_unicity_lesson_user'] = "This user has already signed up";
                }
            }else{      
                $errors['lesson_verify_id'] = "Lesson id is invalid";
            }
            
            return $errors;
        }
        return [];
    }

    private function verifyUserId(int $user_id): bool  // vérification de l'existence de la lesson, du cours
    {
        $UserRepository = new UserRepository();
        $a_user = $UserRepository->getOne($user_id);
        
        return $a_user != [];
    }

    private function verifyLessonId(int $lesson_id): bool  // vérification de l'existence de la lesson, du cours
    {
        $LessonRepository = new LessonRepository();
        $lesson = $LessonRepository->getOne($lesson_id);
        return $lesson != [];
    }

    private function checkAvalaibility(int $lesson_id, LessonUserRepository $LessonUserRepository): bool  // vérification du nombre de places
    {
        $LessonRepository = new LessonRepository();
        $lesson = $LessonRepository->getOne($lesson_id); // array
        if($lesson != [] ){
            $nb_places = $lesson['nb_max_users'];
            $nb_signups = $LessonUserRepository->countUsersByLessonId($lesson_id); 
            if($nb_signups < $nb_places){
                return true;
            }
        }
        return false;
    }

    private function verifyUnicityLessonUser (int $lesson_id, int $user_id) : bool // vérification de l'unicité du couple lesson_id user_id
    {
        $users = $this->getUsersInLesson($lesson_id, $this->LessonUserRepository);
        return !in_array($user_id, $users, true);
    }

    private function getUsersInLesson( int $lesson_id, LessonUserRepository $LessonUserRepository ) : array
    {
        $users = $LessonUserRepository->registeredUsers($lesson_id);
        return $users;
    }






}