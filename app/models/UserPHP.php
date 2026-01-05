<?php

namespace models;

class UserPHP extends Main
{
    public static $table = "users";

    public static $columnDB = ["id", "nombre", "apellido", "email", "password", "confirmado", "token", "admin"];

    public $id;
    public $admin;
    public $nombre;
    public $apellido;
    public $token;
    public $email;
    public $confirmado;
    public $password_c;
    public $password;


    public function __construct($args = [])
    {
        $this->id = $this::$db->real_escape_string($args["id"] ?? null);
        // ✅ CAMBIADO: Usar "nombre" consistentemente (o cambiar a "name" en todas partes)
        $this->nombre = $this::$db->real_escape_string($args["nombre"] ?? $args["name"] ?? "");
        $this->apellido = $this::$db->real_escape_string($args["apellido"] ?? "");
        $this->email = $this::$db->real_escape_string($args["email"] ?? "");
        $this->confirmado = $this::$db->real_escape_string($args["confirmado"] ?? 0);
        $this->token = $this::$db->real_escape_string($args["token"] ?? "");
        $this->password = $this::$db->real_escape_string($args["password"] ?? "");
        $this->password_c = $this::$db->real_escape_string($args["password_c"] ?? "");
        $this->admin = $this::$db->real_escape_string($args["admin"] ?? 0); //en session[] denbe ser rol=area que configuro en las rutas
    }

    public function create_token()
    {
        $token = bin2hex(random_bytes(5));
        $this->token = $token;
        return $token;
    }

    public function Validate_Forget()
    {
        static::$errors = [];

        if (!$this->email) {
            static::$errors["error"][] = "El email es obligatorio";
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            static::$errors["error"][] = "El email no es válido";
        }
        return static::$errors;
    }

    public function Validate_reset()
    {
        static::$errors = [];

        $r = self::findBy("token", $this->token);

        if (!$r) {
            static::$errors["error"][] = "no es valido el token";
        }
        if (!$this->password || $this->password == "") {
            static::$errors["error"][] = "La contraseña es obligatoria";
        } elseif (strlen($this->password) < 6) {
            static::$errors["error"][] = "La contraseña debe tener al menos 6 caracteres";
        }

        if ($this->password !== $this->password_c) {
            static::$errors["error"][] = "Las contraseñas no coinciden";
        }

        return self::$errors;
    }

    public function Validate_Login()
    {
        static::$errors = [];

        if (!$this->email || $this->email == "") {
            static::$errors["error"][] = "El email es obligatorio";
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            static::$errors["error"][] = "El email no es válido";
        }

        if (!$this->password || $this->password == "") {
            static::$errors["error"][] = "La contraseña es obligatoria";
        } elseif (strlen($this->password) < 6) {
            static::$errors["error"][] = "La contraseña debe tener al menos 6 caracteres";
        }
        return static::$errors;
    }

    public function Validate_Register()
    {
        static::$errors = [];

        if (!$this->nombre || !$this->apellido) {
            static::$errors["error"][] = "El nombre y apellido es obligatorio";
        }

        if (!$this->email) {
            static::$errors["error"][] = "El email es obligatorio";
        } elseif (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            static::$errors["error"][] = "El email no es válido";
        }

        if (!$this->password) {
            static::$errors["error"][] = "La contraseña es obligatoria";
        } elseif (strlen($this->password) < 6) {
            static::$errors["error"][] = "La contraseña debe tener al menos 6 caracteres";
        }

        if (!$this->password_c) {
            static::$errors["error"][] = "La confirmación de contraseña es obligatoria";
        } elseif ($this->password !== $this->password_c) {
            static::$errors["error"][] = "Las contraseñas no coinciden";
        }
        if ($this->existeUser()) {
            static::$errors["error"][] = "El email ya está registrado. Por favor, inicia sesión.";
        }
        return static::$errors;
    }

    public function Verify_password($hash, $password)
    {
        return password_verify($password, $hash);
    }

    public function Password_hash($password = null)
    {
        $password = $password ?? $this->password;
        $this->password = password_hash($password, PASSWORD_ARGON2ID);
    }

    public function ExisteUser()
    {
        $r = self::findAllBy("email", $this->email, ["email"]);
        if ($r) {
            return true;
        } else {
            return false;
        }
    }
}
