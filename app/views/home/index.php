<main>
    <div class="contenedor">
        <div class="row">
            <div class="col-md-12">
                <h1>Bienvenido a la pagina principal</h1>
                <p>Esta es una pagina de ejemplo para mostrar el uso de MVC.</p>
            </div>
        </div>

        <div class="js-image-uploader">
            <input type="file" name="files[]" multiple accept="image/*">
        </div>

        <?php

        use components\ComponentManager;

        $component = ComponentManager::make('inputs/inputFile/input-file', []);
        $component->echo();
        ?>

</main>
