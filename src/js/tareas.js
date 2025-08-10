
(() => {
    const buttonTarea = document.querySelector('#agregar-tarea');
    const dashboard = document.querySelector('.dashboard');

    let tareas = [];
    let filtradas = [];
    
    obtenerTareas();

    buttonTarea.addEventListener('click', () => {
        mostrarFormulario();
    });

    // Filtros de busqueda
    const filtros = document.querySelectorAll('#filtros input[type="radio"]');
    filtros.forEach(filtro => {
        filtro.addEventListener('input', filtrarTareas);
    });

    function filtrarTareas(e){
        const filtro = e.target.value;

        if(filtro !== ''){
            filtradas = tareas.filter(tarea => tarea.estado === filtro);
        }else{
            filtradas = [];
        }

        mostrarTareas();
    }

    async function obtenerTareas(){
        try {
            const id = obteniendoProyecto();
            const url = `${location.origin}/api/tareas?id=${id}`;

            const respuesta = await fetch(url);
            const resultado = await respuesta.json();

            tareas = resultado.tareas;

            mostrarTareas();
        } catch (error) {
            console.log(error);
        }
    }

    function mostrarTareas(){
        // Evitando repeticion de las tareas en el HTML
        limpiarTareas();

        // Comprobando las tareas pendientes
        totalPendientes();

        // Comprobando las tareas completadas
        totalCompletadas();
        
        const arrayTareas = filtradas.length ? filtradas : tareas;
        const contenedorTareas = document.querySelector('#listado-tareas');
        
        if(arrayTareas.length === 0){
            const textoNoTareas = document.createElement('li');
            textoNoTareas.textContent = 'No hay Tareas';
            textoNoTareas.classList.add('no-tareas');

            contenedorTareas.appendChild(textoNoTareas);
            return;
        }

        const estadosTarea = {
            0: 'Pendiente',
            1: 'Completada'
        };

        arrayTareas.forEach(tarea => {
            const tareaLista = document.createElement('li');
            tareaLista.dataset.tareaId = tarea.id;
            tareaLista.classList.add('tarea');

            const nombreTarea = document.createElement('p');
            nombreTarea.textContent = tarea.nombre;
            nombreTarea.ondblclick = () => {
                mostrarFormulario(true, {...tarea});
            };

            const divOpciones = document.createElement('div');
            divOpciones.classList.add('opciones');

            // botones
            const btnEstadoTarea = document.createElement('button');
            btnEstadoTarea.classList.add('estado-tarea');
            btnEstadoTarea.classList.add(`${estadosTarea[tarea.estado]}`.toLowerCase());
            btnEstadoTarea.textContent = estadosTarea[tarea.estado];
            btnEstadoTarea.dataset.estadoTarea = tarea.estado;
            btnEstadoTarea.ondblclick = () => {
                cambiarEstadoTarea({...tarea});
            }

            const btnEliminarTarea = document.createElement('button');
            btnEliminarTarea.classList.add('eliminar-tarea');
            btnEliminarTarea.dataset.idTarea = tarea.id;
            btnEliminarTarea.textContent = 'Eliminar';
            btnEliminarTarea.ondblclick = () => {
                confirmarEliminarTarea({...tarea})
            };

            divOpciones.appendChild(btnEstadoTarea);
            divOpciones.appendChild(btnEliminarTarea);

            tareaLista.appendChild(nombreTarea);
            tareaLista.appendChild(divOpciones);

            contenedorTareas.appendChild(tareaLista);
        });
    }

    function totalPendientes(){
        const totalPendientes = tareas.filter(tarea => tarea.estado === '0');
        const pendientesRadio = document.querySelector('#pendientes');

        if(totalPendientes.length === 0){
            pendientesRadio.disabled = true;
        }else{
            pendientesRadio.disabled = false;
        }
    }

    function totalCompletadas(){
        const totalCompletadas = tareas.filter(tarea => tarea.estado === '1');
        const completadasRadio = document.querySelector('#completadas');

        if(totalCompletadas.length === 0){
            completadasRadio.disabled = true;
        }else{
            completadasRadio.disabled = false;
        }
    }

    function mostrarFormulario(editar = false, tarea = {}){
        const modal = document.createElement('div');
        modal.classList.add('modal');
        
        modal.innerHTML = `
            <form class="formulario nueva-tarea" >
                <legend>${editar ? 'Editar Tarea' : 'Añade una nueva tarea'}</legend>

                <div class="campo">
                    <label for="tarea">Tarea</label>
                    <input 
                        type="text" 
                        name="tarea" 
                        id="tarea"
                        value="${tarea.nombre ? tarea.nombre : ''}"
                        placeholder="${tarea.nombre ? 'Edita la tarea' : 'Añadir tarea al proyecto actual'}" />
                </div>

                <div class="opciones">
                    <input type="submit" class="submit-tarea" value="${tarea.nombre ? 'Guardar cambios' : 'Añadir tarea'}" />
                    <button type="button" class="cerrar-modal">Cancelar</button>
                </div>
            </form>
        `;

        setTimeout(() => {
            const formulario = document.querySelector('.formulario');
            formulario.classList.add('show');
        }, 1000);

        modal.addEventListener('click', (e) => {
            seleccionandoBoton(e, modal, editar, tarea);
        });

        dashboard.appendChild(modal);
    }

    function seleccionandoBoton(e, element, editar = false, tarea = {}){
        e.preventDefault();

        if(e.target.classList.contains('cerrar-modal')){
            const formulario = document.querySelector('.formulario');
            formulario.classList.add('close');

            setTimeout(() => {
                element.remove();
            }, 500);
        }

        if(e.target.classList.contains('submit-tarea')){
            const nombreTarea = document.querySelector('#tarea').value.trim();
        
            if(nombreTarea === ''){
                // mostrar alerta
                mostrarAlerta('error', 'El nombre de la tarea es obligatorio', document.querySelector('.formulario legend'));
                return;
            }

            // Llamando funciones en dependiendo de la accion (crear / actualizar)
            if(editar){
                tarea.nombre = nombreTarea;
                actualizarTarea(tarea);
            }else{
                agregarTarea(nombreTarea);
            }
        }
    }

    // Consultar al servidor para agregar una nueva tarea al proyecto
    async function agregarTarea(tarea){
        const url = `${location.origin}/api/tarea`;
        
        // Construyendo peticion
        const datos = new FormData();
        datos.append('nombre', tarea);
        datos.append('proyecto_id', obteniendoProyecto());

        try {
            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();

            mostrarAlerta(resultado.tipo, resultado.mensaje, document.querySelector('.formulario legend'));


            if(resultado.tipo === 'exito'){
                const modal = document.querySelector('.modal');
                
                setTimeout(() => {
                    modal.remove();
                }, 3000);

                // Agregando objeto de tareas al global de tareas
                const tareaObj = {
                    id: String(resultado.id),
                    nombre: tarea,
                    estado: '0',
                    proyecto_id: resultado.proyecto_id
                }

                // Añadiendo objeto y visualizando cambios
                tareas = [...tareas, tareaObj];
                mostrarTareas();
            }
            
        } catch (error) {
            console.log(error);
        }
    }

    function cambiarEstadoTarea(tarea){
        // Creando nuevo estado
        const nuevoEstado = tarea.estado === '1' ? '0' : '1';

        // Modificando estado actual
        tarea.estado = nuevoEstado;

        // Actualizando tarea
        actualizarTarea(tarea);
    }

    async function actualizarTarea(tarea){
        const { id, nombre, estado } = tarea;

        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('proyecto_id', obteniendoProyecto());

        try {
            const url = `${location.origin}/api/tarea/actualizar`;

            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            const resultado = await respuesta.json();
            
            if(resultado.tipo === 'exito'){
                Swal.fire(
                    resultado.mensaje,
                    resultado.mensaje,
                    'success'
                );
            }

            // Eliminando modal
            const modal = document.querySelector('.modal');
            if(modal){
                modal.remove();
            }

            // Verificando tarea a modificar
            tareas = tareas.map(tareaMem => {
                if(tareaMem.id === id){
                    tareaMem.estado = estado;
                    tareaMem.nombre = nombre;
                }

                return tareaMem;
            });

            mostrarTareas();
        } catch (error) {
            console.log(error);
        }
    }

    function confirmarEliminarTarea(tarea){
        Swal.fire({
            title: "¿Estas seguro de eliminar esta tarea?",
            showCancelButton: true,
            confirmButtonText: "Si",
            cancelButtonText: `No`
            }).then((result) => {
            /* Read more about isConfirmed, isDenied below */
            if (result.isConfirmed) {
                eliminandoTarea(tarea);
            }
        });
    }

    async function eliminandoTarea(tarea){
        const { id, nombre, estado } = tarea;

        const datos = new FormData();
        datos.append('id', id);
        datos.append('nombre', nombre);
        datos.append('estado', estado);
        datos.append('proyecto_id', obteniendoProyecto());

        try {
            const url  = `${location.origin}/api/tarea/eliminar`;

            const respuesta = await fetch(url, {
                method: 'POST',
                body: datos
            });

            
            const resultado = await respuesta.json();
            
            if(resultado.resultado){
                mostrarAlerta(
                    resultado.tipo, 
                    resultado.mensaje, 
                    document.querySelector('.contenedor-nueva-tarea')
                );

                tareas = tareas.filter(tareaMem => tareaMem.id !== tarea.id);
                mostrarTareas();
            }
            
        } catch (error) {
            console.log(error);
        }
    }

    function obteniendoProyecto(){
        // Leyendo valores de la URL
        const proyectoParams = new URLSearchParams(window.location.search);
        const proyecto = Object.fromEntries(proyectoParams.entries());

        return proyecto.id;
    }

    function mostrarAlerta(tipo, msg, referencia){
        // Previene multiples alertas
        const alertaPrevia = document.querySelector('.alerta');

        if(alertaPrevia){
            alertaPrevia.remove();
        }

        const alerta = document.createElement('div');
        alerta.classList.add('alerta', tipo);
        alerta.textContent = msg;

        // Inserta alerta despues del legend
        referencia.parentElement.insertBefore(alerta, referencia.nextElementSibling);

        // Eliminando alerta
        setTimeout(() => {
            alerta.remove();
        }, 5000);
    }

    function limpiarTareas(){
        const listadoTareas = document.querySelector('#listado-tareas');

        while(listadoTareas.firstChild){
            listadoTareas.removeChild(listadoTareas.firstChild);
        }
    }

})();