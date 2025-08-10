
<div class="contenedor reestablecer">
    <?php include_once __DIR__ . '/../templates/nombre-sitio.php'; ?>

    <div class="contenedor-sm">
        <p class="descripcion-pagina">Coloca tu nuevo password</p>

        <?php include_once __DIR__ . '/../templates/alertas.php'; ?>

        <?php if($mostrar):  ?>
            
            <form method="post" class="formulario">
                <div class="campo">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Ej: 1234">
                </div>
                
                <input type="submit" value="Reestablecer Password" class="boton">
            </form>
        <?php endif;  ?>
            
            <div class="acciones">
                <a href="/crear">¿Aún no tienes una cuenta? Obten una</a>
                <a href="/olvide">¿Olvidaste tu password?</a>
            </div>
    </div>
</div>




