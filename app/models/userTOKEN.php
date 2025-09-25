<?php
namespace models;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
require_once __DIR__ . "/../../config/Environment.php";
\Environment::load();
class user extends main{
   
    public static $table="users";
    static $columnDB = ["id", "nombre", "email", "password", "confirmado", "tienda_id", "token", "moderador", "created_at", "updated_at", "img"];
    public $id;
    public $nombre;
    public $email;
    public $password; 
    public $confirmado;
    public $tienda_id;
    public $token; 
    public $moderador;
  
    public $created_at;
    public $updated_at; 
    public $img;
    public $password_c; 

    static private $key = \Environment::get('JWT_KEY', 'prE!X2wW^*gH0MQ');
    
    public function __construct($args=[], $img=[])
    {
        $this->id = isset($args["id"]) ? self::$db->real_escape_string($args["id"]) : null;
        $this->nombre = isset($args["nombre"]) ? self::$db->real_escape_string($args["nombre"]) : "";
        $this->email = isset($args["email"]) ? self::$db->real_escape_string($args["email"]) : "";
        $this->password = isset($args["password"]) ? self::$db->real_escape_string($args["password"]) : "";
        $this->password_c = isset($args["password_c"]) ? self::$db->real_escape_string($args["password_c"]) : "";
        $this->confirmado = isset($args["confirmado"]) ? (bool)self::$db->real_escape_string($args["confirmado"]) : 0;
        $this->tienda_id = isset($args["tienda_id"]) ? $args["tienda_id"] : null;
        $this->token = isset($args["token"]) ? self::$db->real_escape_string($args["token"]) : "";
        $this->moderador = isset($args["moderador"]) ? (bool)self::$db->real_escape_string($args["moderador"]) : 0;
       $this->created_at = isset($args["created_at"]) ? self::$db->real_escape_string($args["created_at"]) : date('Y-m-d H:i:s');
        $this->updated_at = isset($args["updated_at"]) ? self::$db->real_escape_string($args["updated_at"]) : date('Y-m-d H:i:s');
        $this->img = !empty($img) ? $img : "";
    }

    public function create_token(){
        $token = bin2hex(random_bytes(8));
        $this->token = $token;
        return $token;
    }

    public function reset(){
        $this->password_hash();
        $stmt = self::$db->prepare("UPDATE users SET password = ? ,token = '' WHERE token = ?");
        $stmt->bind_param("ss", $this->password, $this->token);
        $stmt->execute();
        $stmt->close();
        
        // Limpiar caché completo después de reset de contraseña
        self::clearCache();
    }

  
    public static function desifrartoken(){
        $key=self::$key;
        $token = $_COOKIE['access_token'] ?? null;
            if($token){
                try{
                $payload = JWT::decode($token, new Key($key, 'HS256'));
                } catch (\Throwable $e) {
                    return false;
                }
                $uid = isset($payload->id) ? (int)$payload->id : 0;
                if($uid <= 0){
                    return false;
                }
                $user=user::find($uid);
                if(!$user){
                    return false;
                }else{
                    return $user;
                }
            }else{
                return false;
            }
      
        
      }   
    
    public function resetEmail(){
        $r = self::findBy("email", $this->email);
        $token = $this->create_token();
        $email = new email();

        $email->enviarRecuperacionPassword($this->email, $token, $r->nombre);
        $stmt = self::$db->prepare("UPDATE " . static::$table . " SET token = ? WHERE email = ?");
        $stmt->bind_param("ss",$token, $this->email,);
        $stmt->execute();
        $stmt->close();
        
        // Limpiar caché completo después de actualizar email
        self::clearCache();
        
    }

    public function validate_c(){
       $r=self::findBy("token",$this->token);
      
        if(!$r){  
            static::$errors["error"][]="no es valido el token";  
        } 
        if (!$this->password || $this->password=="") {
            static::$errors["error"][] = "La contraseña es obligatoria";
        } elseif (strlen($this->password) < 6) {
            static::$errors["error"][] = "La contraseña debe tener al menos 6 caracteres";
        }
        return self::$errors;
    }
    public function validate_f(){
       
        $r=$this->existeUser();
        if (!$this->email || $this->email== "") {
            static::$errors["error"][] = "El email es obligatorio";
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            static::$errors["error"][] = "El email no es válido";
        }

        if (!$this->password || $this->password=="") {
            static::$errors["error"][] = "La contraseña es obligatoria";
        } elseif (strlen($this->password) < 6) {
            static::$errors["error"][] = "La contraseña debe tener al menos 6 caracteres";
        }
        return static::$errors;
    }

    public function login() {
        if (!$this->existeUser()) {
            self::$errors["error"][] = "no existe el user";
            return self::$errors;
        }
    
       
        $user = self::findBy("email", $this->email);
    
        if (!$user) {
            self::$errors["error"][] = "no existe el user";
            return self::$errors;
        }
    
        if (!password_verify($this->password, $user->password)) {
            self::$errors["error"][] = "contraseña incorrecta";
            return self::$errors;
        }
    
        if ($user->confirmado != 1) {
            self::$errors["error"][] = "no está confirmado el user";
            return self::$errors;
        }
    
        // Si todo bien, generamos el token
        $key = self::$key;   
        $payload = [
            "id" => $user->id,
            "login" => true,
            "moderador" => $user->moderador ?? 0,
            "img" => $user->img,
            
        ];
    
        $token= JWT::encode($payload, $key, 'HS256');

        setcookie("access_token", $token, [
            'expires' => time() + 3600,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        
        // Limpiar caché después del login exitoso
        self::clearCache();
       
    }

   

    public function validate_r()
    {
       static::$errors = [];

        if (!$this->nombre) {
            static::$errors[] = "El nombre es obligatorio";
        }

        if (!$this->email) {
            static::$errors[] = "El email es obligatorio";
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            static::$errors[] = "El email no es válido";
        }

        if (!$this->password) {
            static::$errors[] = "La contraseña es obligatoria";
        } elseif (strlen($this->password) < 6) {
            static::$errors[] = "La contraseña debe tener al menos 6 caracteres";
        }

        if (!$this->password_c) {
            static::$errors[] = "La confirmación de contraseña es obligatoria";
        } elseif ($this->password !== $this->password_c) {
            static::$errors[] = "Las contraseñas no coinciden";
        }

        if (empty($this->img)) {
            static::$errors[] = "El avatar es obligatorio";
        }
        if($this->existeUser()){
            static::$errors[] = "ya se uso este email";
        }
        return static::$errors;
       
    }

    public function password_hash(){
        $this->password= password_hash($this->password, PASSWORD_ARGON2ID);
    }
   public function varificar(){

    $r=self::findBy("token",$this->token);
    if($r && (int)$r->confirmado === 0 ){
        $this->token = self::$db->real_escape_string($this->token);
        $stmt = self::$db->prepare( "UPDATE " . static::$table . " SET token = '',confirmado=1 where token = ?");
        $stmt->bind_param("s",$this->token);
        $stmt->execute();
        $stmt->close();
        
        // Limpiar caché completo después de verificar usuario
        self::clearCache();
        
        return true;
    }else{
        static::$errors["error"][] = "no es valido o ha expirado";
        return static::$errors;
    }
    }
    public function existeUser(){
        $r=self::findAllBy("email",$this->email,["email"]);
        if($r){
            return true;
        }else{
            return false;
        }
    }   
}