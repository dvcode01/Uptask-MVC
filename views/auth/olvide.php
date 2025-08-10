
<div class="contenedor olvide">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>
    
    <div class="contenedor-sm">
        <p class="descripcion-pagina">Recupera tu acceso a Uptask</p>
        
        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <form action="/olvide" method="post" class="formulario" novalidate>
            <div class="campo">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" placeholder="Ej: correo@correo.com">
            </div>

            <input type="submit" value="Enviar instrucciones" class="boton">
        </form>
        
        <div class="acciones">
            <a href="/crear">¿Aún no tienes una cuenta? Obten una</a>
            <a href="/">¿Ya tienes una cuenta? Inicia sesión</a>
        </div>
    </div>
</div>




