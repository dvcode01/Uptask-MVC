<?php

namespace Controllers;

use Classes\Email;
use Model\Usuario;
use MVC\Router;

class LoginController{
    public static function login(Router $router){
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $auth = new Usuario($_POST);

            $alertas = $auth->validarLogin();

            if(empty($alertas)){
                // Verificando existencia usuario
                $usuario = Usuario::where('email', $auth->email);

                if(!$usuario || !$usuario->confirmado){
                    Usuario::setAlerta('error', 'El usuario no existe o no esta verificado');
                }else{
                    // Comparando si el password auth es igual al password de usuario
                    if(!password_verify($auth->password, $usuario->password)){
                        Usuario::setAlerta('error', 'Password incorrecto');
                    }else{
            
                        // Iniciar sesion 
                        session_start();

                        $_SESSION['id'] = $usuario->id;
                        $_SESSION['nombre'] = $usuario->nombre;
                        $_SESSION['email'] = $usuario->email;
                        $_SESSION['login'] = true;

                        // Redireccion
                        header('Location: /dashboard');

                    }

                   
                }
                
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/login', [
            'pagina' => 'Iniciar sesión',
            'alertas' => $alertas
        ]);
    }

    public static function crear(Router $router){
        $usuario = new Usuario;
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarNuevaCuenta();

            if(empty($alertas)){
                $existeUsuario = Usuario::where('email', $usuario->email);

                if($existeUsuario){
                    Usuario::setAlerta('error', 'El usuario ya esta registrado');
                    $alertas = Usuario::getAlertas();
                }else{
                    // Hasheando password
                    $usuario->hashPassword();

                    // Eliminar password2
                    unset($usuario->password2);

                    // Generando token
                    $usuario->crearToken();

                    // Crear usuario
                    $resultado = $usuario->guardar();

                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarConfirmar();
                   
                    if($resultado){
                        header('Location: /mensaje');
                    }
                    
                }
            }
        }

        $router->render('auth/crear', [
            'pagina' => 'Crear Cuenta',
            'usuario' => $usuario,
            'alertas' => $alertas
        ]);
    }


    public static function olvide(Router $router){
        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = new Usuario($_POST);
            $alertas = $usuario->validarEMail();

            if(empty($alertas)){
                // Busca usuario
                $usuario = Usuario::where('email', $usuario->email);
                
                if($usuario && $usuario->confirmado === '1'){
                    // Generando nuevo token
                    $usuario->crearToken();
                    unset($usuario->password2);
                    
                    // Actualizando usuario
                    $usuario->guardar();

                    // Enviar Email
                    $email = new Email($usuario->email, $usuario->nombre, $usuario->token);
                    $email->enviarInstrucciones();

                    // Mostrar alerta
                    Usuario::setAlerta('exito', 'Hemos enviado las instrucciones a tu email');

                }else{
                    Usuario::setAlerta('error', 'Usuario no existe o no esta confirmado');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/olvide', [
            'pagina' => 'Olvide mi password',
            'alertas' => $alertas,
        ]);
    }

    public static function reestablecer(Router $router){
        $token = s($_GET['token']);
        $mostrar = true;
        $alertas = [];

        // En caso de que el token no exista
        if(!$token) header('Location: /');

        $usuario = Usuario::where('token', $token);
        
        if(empty($usuario)){
            $mostrar = false;
            Usuario::setAlerta('error', 'Token no valido');
        }

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            // Añadiendo nuevo password
            $usuario->sincronizar($_POST);
            unset($usuario->password2);

            // Validando password
            $alertas = $usuario->validarPassword();

            if(empty($alertas)){
                // Hasheando password y eliminando token
                $usuario->token = null;
                $usuario->hashPassword();

                // Guardando en DB
                $resultado = $usuario->guardar();
                
                if($resultado) header('Location: /');
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/reestablecer', [
            'pagina' => 'Reestablecer Password',
            'alertas' => $alertas,
            'mostrar' => $mostrar
        ]);
    }

    public static function mensaje(Router $router){
        

        $router->render('auth/mensaje', [
            'pagina' => 'Cuenta creada correctamente'
        ]);
    }

    public static function confirmar(Router $router){
        $token = $_GET['token'];
        $usuario = Usuario::where('token', $token);

        if(empty($usuario)){
            Usuario::setAlerta('error', 'Token no válido');
        }else{
            $usuario->confirmado = 1;
            $usuario->token = '';
            unset($usuario->password2);

            $usuario->guardar();

            // Enviando mensaje 
            Usuario::setAlerta('exito', 'Cuenta Comprobada Correctamente');
        }

        $alertas = Usuario::getAlertas();

        $router->render('auth/confirmar', [
            'pagina' => 'Confirma Cuenta',
            'alertas' => $alertas
        ]);
    }

    public static function logout(){
        session_start();
        $_SESSION = [];

        header('Location: /');
    }
}



