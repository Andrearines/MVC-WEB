<?php
/**
 * @var array $filters Arreglo de configuración de filtros
 * Cada filtro debe tener:
 * - label: Etiqueta del filtro
 * - id: ID del elemento HTML
 * - type: tipo (text, select, date)
 * - placeholder: (opcional) para inputs de texto
 * - options: (para select) arreglo asociativo de valor => etiqueta
 * - onchange/oninput: (opcional) nombre de la función JS a ejecutar
 * 
 *   $filters = [
 *          [
 *              'label' => 'Número de Boleto',
 *              'id' => 'filterNumber',
 *              'type' => 'text',
 *              'placeholder' => 'Ej: 1001...'
 *          ],
 *          [
 *              'label' => 'Evento',
 *              'id' => 'filterEvent',
 *              'type' => 'select',
 *              'options' => $eventOptions
 *          ],
 *          [
 *              'label' => 'Vendedor',
 *              'id' => 'filterVendedor',
 *              'type' => 'select',
 *              'options' => ['' => 'Todos los vendedores', '0' => 'Sin asignar'] + array_combine(array_map(fn($u) => $u->id, $users), array_map(fn($u) => $u->name, $users)),
 *              'onchange' => 'applyFilters()'
 *          ],
 *          [
 *              'label' => 'Estado',
 *              'id' => 'filterStatus',
 *              'type' => 'select',
 *              'options' => [
 *                  '' => 'Todos los estados',
 *                  'available' => 'disponible',
 *                  'sold' => 'vendido',
 *                  'reservation' => 'reservado',
 *                  'lost' => 'perdido',
 *                  'disabled' => 'desactivado'
 *              ]
 *          ]
 *      ];
 * 
 */
$filters = $filters ?? [];
?>
<link rel="stylesheet" href="/build/components/views/filters/scss/_filters.scss">
<div class="filters-panel" id="filtersPanel" style="display: none;">
    <div class="filters-grid">
        <?php foreach ($filters as $filter): ?>
            <div class="filter-group">
                <label><?php echo $filter['label']; ?></label>

                <?php if ($filter['type'] === 'select'): ?>
                    <select id="<?php echo $filter['id']; ?>" onchange="<?php echo $filter['onchange'] ?? 'applyFilters()'; ?>">
                        <?php foreach ($filter['options'] as $value => $label): ?>
                            <option value="<?php echo $value; ?>"><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($filter['type'] === 'date'): ?>
                    <input type="date" id="<?php echo $filter['id']; ?>"
                        onchange="<?php echo $filter['onchange'] ?? 'applyFilters()'; ?>">
                <?php else: // default to text ?>
                    <input type="text" id="<?php echo $filter['id']; ?>"
                        placeholder="<?php echo $filter['placeholder'] ?? ''; ?>"
                        oninput="<?php echo $filter['oninput'] ?? 'applyFilters()'; ?>">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<script>
    function toggleFiltersPanel() {
        const panel = document.getElementById('filtersPanel');
        if (panel) {
            panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
        }
    }
</script>