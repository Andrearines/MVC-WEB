<?php

// Componentes principales de la aplicación
// Aquí se pueden incluir componentes reutilizables como headers, footers, modales, etc.
// También se pueden incluir componentes de formulario como input-file, select, textarea, button, etc.
// Además de otros componentes como cards, lists, tables, forms, etc.

// Estructura de carpetas recomendada:
// app/components/
//   ├── views/
//   │   ├── inputs/
//   │   │   ├── input-file.php
//   │   │   └── ...
//   │   ├── cards/
//   │   │   └── card.php
//   │   └── ...
//   └── componets.php (este archivo)



namespace components;

class ComponentManager
{
    private string $componentPath;
    private array $data;

    public function __construct(string $componentPath, array $data = [])
    {
        $this->componentPath = $componentPath;
        $this->data = $data;
    }

    public function render(): string
    {
        $filePath = __DIR__ . "/views/{$this->componentPath}.php";

        if (!file_exists($filePath)) {
            return "<!-- Componente no encontrado: {$this->componentPath} en {$filePath} -->";
        }

        // Extraer datos para que estén disponibles en el componente
        extract($this->data);

        // Capturar el output del componente
        ob_start();
        include $filePath;
        return ob_get_clean();
    }

    public function echo(): void
    {
        echo $this->render();
    }

    public static function make(string $componentPath, array $data = []): self
    {
        return new self($componentPath, $data);
    }
}
