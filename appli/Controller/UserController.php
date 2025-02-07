<?php

class UserController
{
    // private UserRepository $repository ;

    public function __construct( private UserRepository $UserRepository )
    {
        // $this->repository = new UserRepository(new Database(DB_HOST, DB_BASE, DB_USER, DB_PASS));
    }


    public function processRequest( string $method, ?string $id) :void  // le point d'interrogation means nullable
    {
        if($id){  // single resource
            $this->processResourceRequest($method,$id);

        } else { // collection
            $this->processCollectionRequest($method);
        }
    }

    private function processResourceRequest(string $method, string $id): void
    {
        $user = $this->UserRepository->getOne($id);

        if(!$user){
            http_response_code(404);
            echo json_encode(['message' => 'User not found']);
            return;
        }

        switch($method){
            case 'GET':
                echo json_encode($user);
                break;
            case 'PATCH':
                $data = (array) json_decode(file_get_contents('php://input'),true);

                $errors = $this->getValidationErrors($data);
                if(!empty($errors)){
                    http_response_code(422);
                    echo json_encode($errors);
                    break;
                }
                
                $rows = $this->UserRepository->update( $user, $data );
                http_response_code(200);
                echo json_encode([
                    'message' => "User id = $id modified",
                    'rows' => $rows
                ]);
                break;
            case 'DELETE':
                $rows = $this->UserRepository->delete($id);
                http_response_code(204);
                echo json_encode([
                    'message' => "User id = $id deleted",
                    'rows' => $rows
                ]);
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
                //$this->getCollection();
                break;
            case 'POST':
                $data = (array) json_decode(file_get_contents('php://input'),true);

                $errors = $this->getValidationErrors($data);
                if(!empty($errors)){
                    http_response_code(422);
                    echo json_encode($errors);
                    break;
                }
                
                $id = $this->UserRepository->create($data);
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


    private function getValidationErrors(array $data): array
    {
        $errors = [];
        if(!isset($data['name']) || empty($data['name'])){
            $errors['name'] = 'Name is required';
        }
        if(!isset($data['age']) || empty($data['age'])){
            $errors['age'] = 'Age is required';
        }
        if( isset($data['age'])  &&  filter_var($data['age'], FILTER_VALIDATE_INT) === false){
            $errors['age'] = 'Age must be a number';
        }
        
        return $errors;
    }


}