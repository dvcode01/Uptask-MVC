<?php

namespace Controllers;

use Model\Proyecto;
use Model\Tarea;

class TareaController{

    public static function index(){
        $id_proyecto = $_GET['id'];

        if(!$id_proyecto) header('Location: /dashboard');

        $proyecto = Proyecto::where('url', $id_proyecto);

        session_start();

        if(!$proyecto || $proyecto->propietario_id !== $_SESSION['id']) header('Location: /404');

        $tareas = Tarea::belongsTo('proyecto_id', $proyecto->id);

        echo json_encode(['tareas' => $tareas]);
    }

    public static function crear(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            session_start();

            $proyecto = Proyecto::where('url', $_POST['proyecto_id']);

            if(!$proyecto || $proyecto->propietario_id !== $_SESSION['id']){
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al agregar la tarea'
                ];

                echo json_encode($respuesta);
                return;
            }

            $tarea = new Tarea($_POST);
            $tarea->proyecto_id = $proyecto->id;

            $resultado = $tarea->guardar();
            $respuesta = [
                'tipo' => 'exito',
                'id' => $resultado['id'],
                'mensaje' => 'Tarea creada correctamente',
                'proyecto_id' => $proyecto->id
            ];

            echo json_encode($respuesta);
        }
    }

    public static function actualizar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            session_start();

            // Validando si proyecto existe
            $proyecto = Proyecto::where('url', $_POST['proyecto_id']);

            if(!$proyecto || $proyecto->propietario_id !== $_SESSION['id']){
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al actualizar la tarea'
                ];

                echo json_encode($respuesta);
                return;
            }

            $tarea = new Tarea($_POST);
            $tarea->proyecto_id = $proyecto->id;

            // Guardando en DB
            $resultado = $tarea->guardar();

            if($resultado){
                $respuesta = [
                    'tipo' => 'exito',
                    'id' => $tarea->id,
                    'proyecto_id' => $proyecto->id,
                    'mensaje' => 'Actualizado Correctamente'
                ];
                
                echo json_encode($respuesta);
            }
        }
    }   
    
    public static function eliminar(){
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            session_start();

            // Validando si proyecto existe
            $proyecto = Proyecto::where('url', $_POST['proyecto_id']);

            if(!$proyecto || $proyecto->propietario_id !== $_SESSION['id']){
                $respuesta = [
                    'tipo' => 'error',
                    'mensaje' => 'Hubo un error al eliminar la tarea'
                ];

                echo json_encode($respuesta);
                return;
            }

            $tarea = new Tarea($_POST);
            $resultado = $tarea->eliminar();

            $resultado = [
                'tipo' => 'exito',
                'resultado' => $resultado,
                'mensaje' => 'Eliminado Correctamente'
            ];
            
            echo json_encode($resultado);
        }
    }
}


