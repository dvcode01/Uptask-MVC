

<aside class="sidebar">
    
    <div class="contenedor-sidebar">
        <h2>Uptask</h2>

        <div class="cerrar-menu">
            <img src="build/img/cerrar.svg" alt="imagen cerrar menu" id="cerrar-menu">
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="/dashboard" class="<?php  echo ($pagina === 'Proyectos') ? 'activo' : ''; ?>">Proyectos</a>
        <a href="/crear-proyecto" class="<?php  echo ($pagina === 'Crear Proyecto') ? 'activo' : '';  ?>">Crear Proyectos</a>
        <a href="/perfil" class="<?php  echo ($pagina === 'Perfil') ? 'activo' : '';  ?>">Perfil</a>
    </nav>

    <div class="cerrar-sesion-mobile">
        <a href="/logout" class="cerrar-sesion">Cerrar Sesión</a>
    </div>
</aside>