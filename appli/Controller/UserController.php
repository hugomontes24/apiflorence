<?php

class UserController
{
    // private UserRepository $repository ;

    public function __construct( private UserRepository $UserRepository )
    {
        // $this->repository = new UserRepository(new Database(DB_HOST, DB_BASE, DB_USER, DB_PASS));
    }

    public function processRequest( string $method, ?int $id) :void  // le point d'interrogation means nullable
    {
        if($id){  // single resource
            $this->processResourceRequest($method,$id);

        } else { // collection
            $this->processCollectionRequest($method);
        }
    }

    private function processResourceRequest(string $method, string $id): void
    {
        $a_user = $this->UserRepository->getOne($id);
       
        if(empty($a_user)){
            http_response_code(404);
            echo json_encode(['message' => 'User not found with this id']);
            return;
        }
        
        $User = new User();
        $User->hydrate($a_user);
        switch($method){
            case 'GET':
                echo json_encode($User->toArray());
                break;
            case 'PATCH':
                $data = (array) json_decode(file_get_contents('php://input'), true);
                //var_dump( $data);
                $NewUser = new User();
                $NewUser->hydrate($data);

                $errors = $this->getValidationErrors($NewUser);
                if(!empty($errors)){
                    http_response_code(422);
                    echo json_encode($errors);
                    break;
                }
                
                $rows = $this->UserRepository->update( $User, $NewUser );
                http_response_code(200);
                echo json_encode([
                    'message' => "User id = $id modified",
                    'rows' => $rows
                ]);
                break;

            case 'DELETE':
                $rows = $this->UserRepository->delete($id);
                http_response_code(204);
                // echo json_encode([
                //     'message' => "User id = $id deleted",
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
                echo json_encode($this->UserRepository->getAll());
                break;
            case 'POST':
                $data = (array) json_decode(file_get_contents('php://input'),true);
                $NewUser = new User();
                $NewUser->hydrate($data);

                $errors = $this->getValidationErrors($NewUser);
                if(!empty($errors)){
                    http_response_code(422);
                    echo json_encode($errors);
                    break;
                }
                
                $id = $this->UserRepository->create($NewUser);
                http_response_code(201);
                echo json_encode([
                    'message' => 'User created',
                    'id' => $id
                ]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST");
        }
    }


    private function getValidationErrors(User $user): array
    {
        $errors = [];
        if(null === $user->getName() || $user->getName() === ''){
            $errors['name'] = 'Name is required';
        }
        if(null === $user->getAge() || $user->getAge() === ''){
            $errors['age'] = 'Age is required';
        }
        // if(!isset($data['age']) || empty($data['age'])){
        //     $errors['age'] = 'Age is required';
        // }
        if(null !== $user->getAge() && filter_var( $user->getAge() , FILTER_VALIDATE_INT) === false){
            $errors['age'] = 'Age must be a number';
        }
        // if( isset($data['age'])  &&  filter_var($data['age'], FILTER_VALIDATE_INT) === false){
        //     $errors['age'] = 'Age must be a number';
        // }    
        return $errors;
    }


}