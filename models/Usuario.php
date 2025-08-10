<?php

namespace Model;

class Usuario extends ActiveRecord{
    protected static $tabla = 'usuarios';
    protected static $columnasDB = ['id', 'nombre', 'email', 'password', 'token', 'confirmado'];

    public $id;
    public $nombre;
    public $email;
    public $password;
    public $password2;
    public $password_actual;
    public $password_nueva;
    public $token;
    public $confirmado;

    public function __construct($args = []) {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->email = $args['email'] ?? '';
        $this->password = $args['password'] ?? '';
        $this->password2 = $args['password2'] ?? '';
        $this->password_actual = $args['password_actual'] ?? '';
        $this->password_nueva = $args['password_nueva'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->confirmado = $args['confirmado'] ?? 0;
    }

    // Validacion cuentas nuevas
    public function validarNuevaCuenta() : Array{
        if(!$this->nombre){
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }

        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }

        if(!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }

        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'El password debe tener un minimo de 6 caracteres';
        }

        if($this->password !== $this->password2){
            self::$alertas['error'][] = 'Los passwords son diferentes';
        }

        return self::$alertas;
    }

    public function validarPassword(): array{
        if(!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }

        if(strlen($this->password) < 6){
            self::$alertas['error'][] = 'El password debe tener un minimo de 6 caracteres';
        }

        return self::$alertas;
    }

    public function validarLogin(): array{
        if(!$this->email){
            self::$alertas['error'][] = 'El email es obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Email no valido';
        }

        if(!$this->password){
            self::$alertas['error'][] = 'El password es obligatorio';
        }

        return self::$alertas;
    }

    public function comprobarPasswordActual(): bool{
        return password_verify($this->password_actual, $this->password);
    }

    public function hashPassword(): void{
        $this->password = password_hash($this->password, PASSWORD_BCRYPT);
    }

    public function crearToken(): void{
        $this->token = uniqid();
    }

    public function validarEmail(): array{
        if(!$this->email){
            self::$alertas['error'][] = 'El Email es obligatorio';
        }

        if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
            self::$alertas['error'][] = 'Email no valido';
        }

        return self::$alertas;
    }


    public function validarPerfil(): array{
        if(!$this->nombre){
            self::$alertas['error'][] = 'El nombre es obligatorio';
        }

        if(!$this->email){
            self::$alertas['error'][] = 'El Email es obligatorio';
        }

        return self::$alertas;
    }

    public function nuevoPassword(): array{
        if(!$this->password_actual){
            self::$alertas['error'][] = 'El password actual no puede ir vacío';
        }


        if(!$this->password_nueva){
            self::$alertas['error'][] = 'El password nuevo no puede ir vacío';
        }

        if(strlen($this->password_nueva) < 6){
            self::$alertas['error'][] = 'El password nuevo debe tener minimo 6 caracteres';
        }

        return self::$alertas;
    }

}

