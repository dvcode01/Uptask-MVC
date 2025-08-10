
<div class="contenedor crear">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Crear Cuenta en Uptask</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>


        <form action="/crear" method="post" class="formulario">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" 
                       name="nombre" 
                       id="nombre" 
                       placeholder="Ej: Marco"
                       value="<?php echo $usuario->nombre; ?>">
            </div>

            <div class="campo">
                <label for="email">Email</label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       placeholder="Ej: correo@correo.com"
                       value="<?php echo $usuario->email; ?>">
            </div>

            <div class="campo">
                <label for="password">Password</label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       placeholder="Ej: 1234">
            </div>

            <div class="campo">
                <label for="password2">Repetir Password</label>
                <input type="password" name="password2" id="password2" placeholder="Ej: 1234">
            </div>

            <input type="submit" value="Crear Cuenta" class="boton">
        </form>
        
        <div class="acciones">
            <a href="/">¿Ya tienes una cuenta? Inicia sesión</a>
            <a href="/olvide">¿Olvidaste tu password?</a>
        </div>
    </div>
</div>




