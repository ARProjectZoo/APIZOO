<?php 
use \Firebase\JWT\JWT;
define('MY_KEY', 'tokens_key');
class Controller_Base extends Controller_Rest
{
	private static $secret_key = 'ARZOO';
    private static $encrypt = ['HS256'];
    private static $aud = null;
    public function post_config(){
        $admin = Model_Users::find('all',
                                    array('where' => array(
		            							array('id_role', '=', 1)
		            							)
		            						)
		            					);
        if(empty($admin))
        {
            $adminRole = new Model_Roles();
            $adminRole->userName = "admin";
            $adminRole->save();
            
            $userRole = new Model_Roles();
            $userRole->userName = "user";
            $userRole->save();
            $admin = new Model_Users();
            $admin->userName = "admin";
            $admin->password = "1234";
            $admin->email = "admin@admin.es";
            $admin->role = Model_Roles::find($adminRole->id);
            $admin->save();
            $json = $this->response(array(
                'code' => 201,
                'message' => 'Configuración terminada correctamente',
                'data' => $admin,
            ));
            return $json;
        }
        else
        {
            $json = $this->response(array(
                'code' => 401,
                'message' => 'Configuración ya implementada anteriormente',
                'data' => null,
            ));
            return $json;
        }
    }
    protected function encodeData($data)
    {
        $encodeData = JWT::encode($data, MY_KEY);
        return $encodeData;
    }
	protected function encodeToken($userName, $password, $id, $email, $id_role)
    {
        $token = array(
        		"id" => $id,
                "userName" => $userName,
                "password" => $password,
                "email" => $email,
                "role" => $id_role,
        );
        $encodedToken = JWT::encode($token, MY_KEY);
        return $encodedToken;
    }
    protected function decodeToken()
    {
        $header = apache_request_headers();
        $token = $header['Authorization'];
        if(!empty($token))
        {
            $decodedToken = JWT::decode($token, MY_KEY, array('HS256'));
            return $decodedToken;
        }      
    }
    protected function authenticate(){
        try {
               
            $header = apache_request_headers();
            $token = $header['Authorization'];
            if(!empty($token))
            {
                $decodedToken = JWT::decode($token, MY_KEY, array('HS256'));
                $query = Model_Users::find('all', 
                    ['where' => ['userName' => $decodedToken->userName, 
                                 'password' => $decodedToken->password, 
                                 'id_role' => $decodedToken->role,
                                 'email' => $decodedToken->email,
                                 'id' => $decodedToken->id]]);
                if($query != null)
                {
                    return true;
                }else{
                    return false;
                }
            }else{
                return false;
            }
        } 
        catch (Exception $UnexpectedValueException)
        {
            return false;
        }
    }
    public function get_default_auth()
    {  
        $auth = self::authenticate();
        if($auth == true)
        {
            $json = $this->response(array(
                    'code' => 200,
                    'message' => 'Usuario autenticado',
                    'data' => null
            ));
            return $json;
        }else{
            $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Usuarios no autenticado',
                    'data' => null
            ));
            return $json;
        }
        
    }
}