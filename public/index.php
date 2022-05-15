<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../includes/Dboperations.php';

$app = AppFactory::create();
$app->setBasePath('/MyApi');

$app->get('/hello/{name}', function (Request $request, Response $response, array $args) {
    $name = $args['name'];
    $response->getBody()->write("Hello, $name");
    return $response;
});

$app->post('/tealogin', function (Request $request, Response $response) {
    if (!haveEmptyParam(['table','username','password'], $response)) {
        $request_data=$request->getParsedBody();
        $table=$request_data['table'];
        $username= $request_data['username'];
        $password = $request_data['password'];
        $db = new DbOperations();
        $message= [];
        if ($db->userLogin($table, $username, $password)) {
            $user = $db->getIns($username);
            $message['error'] = false;
            $message['id'] = $user;
            $message['Message'] = "Login successful!";

            $response->getBody()->write(json_encode($message));
            
            return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(201);
        } else {
            $message['error'] = true;
            $message['Message'] = "Invalid username or password";
            $response->getBody()->write(json_encode($message));

            return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(422);
        }
    } else {
        return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(422);
    }
});


$app->post('/stulogin', function (Request $request, Response $response) {
    if (!haveEmptyParam(['table','username','password'], $response)) {
        $request_data=$request->getParsedBody();
        $table=$request_data['table'];
        $username= $request_data['username'];
        $password = $request_data['password'];
        $db = new DbOperations();
        $message= [];
        if ($db->userLogin($table, $username, $password)) {
            $user = $db->getStudent($username);
            $message['error'] = false;
            $message['id'] = $user;
            $message['Message'] = "Login successful!";
    
            $response->getBody()->write(json_encode($message));
                
            return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(201);
        } else {
            $message['error'] = true;
            $message['Message'] = "Invalid username or password";
            $response->getBody()->write(json_encode($message));
    
            return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(422);
        }
    } else {
        return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(422);
    }
});

$app->post('/courseselector',function (Request $request, Response $response){
    $request_data=$request->getParsedBody();
    $current_instructor = $request_data['select'];
    $db = new Dboperations(); 
    if(isset($current_instructor)){
        $message['courselist']=$db->get_course($current_instructor);
        $message['error'] =FALSE;
        $message['Message']='Fetched Successfully';
        $response->getBody()->write(json_encode($message));
        return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(201);
    }
    else{
        $message['error'] =TRUE;
        $message['Message']='Received Empty parameter';
        $response->getBody()->write(json_encode($message));
        return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(422);
    }
});

$app->post('/sectionselector', function(Request $request, Response $response){
    $request_data=$request->getParsedBody();
    $current_course = $request_data['select'];
    $db = new Dboperations();
    if(isset($current_course)){
        $message['sectionlist']=$db->get_section($current_course);
        $message['error'] =FALSE;
        $message['Message']='Fetched Successfully';
        $response->getBody()->write(json_encode($message));
        return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(201);
    }
    else{
        $message['error'] =TRUE;
        $message['Message']='Received Empty parameter';
        $response->getBody()->write(json_encode($message));
        return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(422);
    }
});

$app->post('/studentlist', function(Request $request, Response $response){
    $request_data = $request->getParsedBody();
    $current_section = $request_data['select'];
    $db = new Dboperations();
    if(isset($current_section)){
        $message['idlist']=$db->get_studentid($current_section);
        $message['namelist'] = $db->get_studentname($current_section);
        $message['error'] =FALSE;
        $message['Message']='Fetched Successfully';
        $response->getBody()->write(json_encode($message));
        return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(201);
    }
    else{
        $message['error'] =TRUE;
        $message['Message']='Received Empty parameter';
        $response->getBody()->write(json_encode($message));
        return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(422);
    }


});

$app->post('/studcourselister',function(Request $request, Response $response){
    $request_data = $request->getParsedBody();
    $current_student = $request_data['select'];
    $db = new Dboperations();
    $percent = array();
    if(isset($current_student)){
        
        $courses=$db->get_studentcourses($current_student);
        $message['courselist']=$courses;
        foreach($courses as $course){
            array_push($percent, $db->get_total_session($current_student, $course));
        }
        $message['percentage']=$percent;
        $message['error'] =FALSE;
        $message['Message']='Fetched Successfully';
        $response->getBody()->write(json_encode($message));
        return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(201);
    }
    else{
        $message['error'] =TRUE;
        $message['Message']='Received Empty parameter';
        $response->getBody()->write(json_encode($message));
        return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(422);
    }

});

$app->post('/fetchhistory',function(Request $request, Response $response){
    $request_data = $request->getParsedBody();
    $current_student = $request_data['id'];
    $current_course = $request_data['course'];
    $db = new Dboperations();
    if(isset($current_student)){
        $message['percentage']=$db->get_total_session_roll($current_student, $current_course);
        $message['error'] =FALSE;
        $message['Message']='Fetched Successfully';
        $response->getBody()->write(json_encode($message));
        return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(201);
    }
    else{
        $message['error'] =TRUE;
        $message['Message']='Received Empty parameter';
        $response->getBody()->write(json_encode($message));
        return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(422);
    }

});

$app->post('/changepass',function(Request $request, Response $response){
    $request_data = $request->getParsedBody();
    $user_id = $request_data['id'];
    $table = $request_data['table'];
    $old_pass=$request_data['old'];
    $new_pass=$request_data['pass'];
    $db = new Dboperations();
    if(($db->change_password($table,$user_id,$old_pass,$new_pass))=='1'){
        $message['error'] =FALSE;
        $message['Message']='Password changed Successfully!';
        $response->getBody()->write(json_encode($message));
        return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(201);
    }
    else{
        $message['error'] =TRUE;
        $message['Message']='Please use the correct old password!';
        $response->getBody()->write(json_encode($message));
        return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(422);
    }
});

$app->post('/saveattendance',function(Request $request, Response $response){
    $request_data = $request->getParsedBody();
    $present_string = $request_data['present'];
    $permission_string = $request_data['permission'];
    $session_string=$request_data['session'];

    if(isset($present_string)&&isset($session_string)){
    $present =explode(",",$present_string);
    $db= new Dboperations();
    $session =explode(",",$session_string);

        if(!is_null($permission_string)){
            $permission =explode(",",$permission_string);
            $message['Message']=$db->insert_all($session,$present,$permission);
            $message['error'] =FALSE;
            $response->getBody()->write(json_encode($message));
            return $response
                        ->withHeader('content-type', 'application/json')
                        ->withStatus(201);
        }
        else{
            $message['Message']=$db->insert($session,$present);
            $message['error'] =FALSE;
            $response->getBody()->write(json_encode($message));
            return $response
                        ->withHeader('content-type', 'application/json')
                        ->withStatus(201);
        }
    }
        else{
        $message['error'] =TRUE;
        $message['Message']='There is no data from The Client!';
        $response->getBody()->write(json_encode($message));
        return $response
                    ->withHeader('content-type', 'application/json')
                    ->withStatus(422);
                }
});

function haveEmptyParam($required_params, $response)
{
    $error =false;
    $error_params = '';
    $request_params = $_REQUEST;

    foreach ($required_params as $param) {
        if (!isset($request_params[$param]) || strlen($request_params[$param])<=0) {
            $error = true;
            $error_params .= $param . ' , ';
        }
    }
    if ($error) {
        $error_detail= [];
        $error_detail['error']= true;
        $error_detail['Message']= 'Required parameters '.substr($error_params, 0, -2). ' is missing.';
        $response->getBody()->Write(json_encode($error_detail));
    }
    return $error;
}


$app->run();
