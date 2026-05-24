<?php

namespace validator;
use errors\Errors;

class ValidatorModels
{

    private Errors $Errors;

    public function __construct()
    {
        $this->Errors = new Errors();
    }

    /**
     * Valida los datos según las reglas especificadas.
     * Soporta reglas como texto separado por '|' o como arreglos.
     * 
     * @param array $data Los datos a validar
     * @param array|string $rules Las reglas de validación
     * @return void
     */
    public function validate(array $data, array $rules): void
    {
        $this->clearErrors();

        foreach ($rules as $field => $fieldRules) {
            $parsedRules = $this->parseRules($fieldRules);
            $value = $data[$field] ?? null;

            // Verificamos de forma segura si es requerido
            $isRequired = in_array('required', $parsedRules, true);
            $isEmpty = ($value === null || $value === '');

            // Si es requerido y viene vacío, detenemos la validación de ese campo
            if ($isRequired && $isEmpty) {
                $this->Errors->addError($field, "El campo {$field} es requerido");
                continue;
            }

            // Si vino vacío y no era requerido, ignoramos validaciones de tipo
            if ($isEmpty) {
                continue;
            }

            // Procedemos a evaluar cada subtipo de regla (ej: string, numeric, email...)
            foreach ($parsedRules as $ruleName) {
                // Ya validamos required
                if ($ruleName === 'required') {
                    continue;
                }

                $this->applyRule($field, $value, $ruleName);
            }
        }
    }

    /**
     * Extrae las reglas desde un array o un texto separado por '|'.
     */

    private function parseRules($rules): array
    {
        if (is_array($rules)) {
            return $rules;
        }

        if (is_string($rules)) {
            // Separa "required|string" en ['required', 'string']
            return explode('|', $rules);
        }

        return [];
    }

    /**
     * Evalúa el tipo de regla específica.
     */
    private function applyRule(string $field, $value, string $rule): void
    {
        // Dividir por ':' en caso de que pasen un límite como max:255 (a futuro)
        $parts = explode(':', $rule);
        $ruleName = trim($parts[0]);
        // $param = $parts[1] ?? null;

        switch ($ruleName) {
            case 'numeric':
                if (!is_numeric($value)) {
                    $this->Errors->addError($field, "El campo {$field} debe ser número (entero o decimal)");
                }
                break;
            case 'integer':
                // Validación estricta para números enteros
                if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                    $this->Errors->addError($field, "El campo {$field} debe ser un número entero válido");
                }
                break;
            case 'string':
                if (!is_string($value)) {
                    $this->Errors->addError($field, "El campo {$field} debe ser un texto");
                }
                break;
            case 'email':
                if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                    $this->Errors->addError($field, "El campo {$field} debe ser un correo electrónico válido");
                }
                break;
            case 'array':
                if (!is_array($value)) {
                    $this->Errors->addError($field, "El campo {$field} debe ser una lista (arreglo)");
                }
                break;
            case 'boolean': // Valida si es bool puro o string de true/false
                if (!is_bool($value) && !in_array($value, [0, 1, '0', '1', true, false], true)) {
                    $this->Errors->addError($field, "El campo {$field} debe ser verdadero o falso (boolean)");
                }
                break;
            default:
                // Puedes optar por ignorar reglas no programadas o logearlas
                break;
        }
    }

    /**
     * Agrega el mensaje al array del campo en cuestión.
     */

    public function hasErrors(): bool
    {
        return $this->Errors->hasErrors();
    }

    public function clearErrors(): void
    {
        $this->Errors->clearErrors();
    }
    public function getErrors()
    {
        return $this->Errors->getErrors();
    }
}