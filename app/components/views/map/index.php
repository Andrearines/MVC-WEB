<?php
// Coordenadas dinámicas (ejemplo)
$lat ?? 14.0723;   // Latitud
$lng ?? -87.1921;  // Longitud
$zoom ?? 13;
?>

<div>
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <div id="map"></div>

    <!-- Variables PHP → JS -->
    <script>
        const MAP_LAT = <?= $lat ?>;
        const MAP_LNG = <?= $lng ?>;
        const MAP_ZOOM = <?= $zoom ?>;
    </script>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="/build/js/map.js"></script>

</div>
