<?php

namespace Controllers;

use Model\Proyecto;
use Model\Usuario;
use MVC\Router;

class DashboardController{

    public static function index(Router $router){
        session_start();

        // Verificando autenticacion usuario
        isAuth();

        // Obtener proyectos del usuario
        $proyectos = Proyecto::belongsTo('propietario_id', $_SESSION['id']);
        
        $router->render('dashboard/index', [
            'pagina' => 'Proyectos',
            'proyectos' => $proyectos
        ]);
    }

    public static function crear_proyecto(Router $router){
        session_start();

        // Verificando autenticacion usuario
        isAuth();
        $alertas = [];
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $proyecto = new Proyecto($_POST);
            
            // Validacion de los campos
            $alertas = $proyecto->validarProyecto();

            if(empty($alertas)){
                // Generar URL Unica
                $proyecto->crearURL();

                // Asignando id del usuario
                $proyecto->propietario_id = $_SESSION['id'];

                // Guardar proyecto
                $proyecto->guardar();

                // Redireccionando
                header("Location: /proyecto?id=$proyecto->url");
            }
        }

        $router->render('dashboard/crear-proyecto', [
            'pagina' => 'Crear Proyecto',
            'alertas' => $alertas
        ]);
    }

    public static function proyecto(Router $router){
        session_start();

        // Verificando autenticacion usuario
        isAuth();

        // Comprobando si existe el id del proyecto en la URL
        $token = $_GET['id'];
        
        if(!$token) header('Location: /');

        // Verificar que la persona que visita el proyecto, es quien lo creo
        $proyecto = Proyecto::where('url', $token); 
        
        if($proyecto->propietario_id !== $_SESSION['id']) header('Location: /');
        
        $router->render('dashboard/proyecto', [
            'pagina' => $proyecto->proyecto
        ]);
    }

    public static function perfil(Router $router){
        session_start();
        isAuth();

        $alertas = [];
        $usuario = Usuario::find($_SESSION['id']);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario->sincronizar($_POST);
            $alertas = $usuario->validarPerfil();

            if(empty($alertas)){
                // Verificar email pertenece no pertenece a otro usuario
                $existeUsuario = Usuario::where('email', $usuario->email);
                
                if($existeUsuario && $existeUsuario->id !== $usuario->id){
                    // Mostrar alerta error
                    Usuario::setAlerta('error', 'Email no válido, ya pertenece a otra cuenta');
                }else{
                    // Guardando usuario
                    $usuario->guardar();

                    // Generando alerta
                    Usuario::setAlerta('exito', 'Guardado Correctamente');
    
                    // Reescribiendo el nombre de la barra
                    $_SESSION['nombre'] = $usuario->nombre;
                }

            }

        }

        $alertas = $usuario->getAlertas();

        $router->render('dashboard/perfil', [
            'pagina' => 'Perfil',
            'alertas' => $alertas,
            'usuario' => $usuario
        ]);
    }

    public static function cambiar_password(Router $router){
        session_start();
        isAuth();

        $alertas = [];

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            $usuario = Usuario::find($_SESSION['id']);

            // Sincronizar con los datos del formulario
            $usuario->sincronizar($_POST);

            $alertas = $usuario->nuevoPassword();

            if(empty($alertas)){
                $resultado = $usuario->comprobarPasswordActual();

                if($resultado){
                    // Cambiando password y hasheando
                    $usuario->password = $usuario->password_nueva;
                    $usuario->hashPassword();

                    // Borrando propiedades innecesarias
                    unset($usuario->password2);
                    unset($usuario->password_actual);
                    unset($usuario->password_nueva);

                    // Actualizar password
                    $resultado = $usuario->guardar();

                    if($resultado){
                        Usuario::setAlerta('exito', 'Guardado Correctamente');
                    }
                }else{
                    Usuario::setAlerta('error', 'Password incorrecto');
                }
            }
        }

        $alertas = Usuario::getAlertas();

        $router->render('dashboard/cambiar-password', [
            'pagina' => 'Cambiar Password',
            'alertas' => $alertas
        ]);
    }

    
}







