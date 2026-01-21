// Inicializar el mapa
const map = L.map("map").setView([MAP_LAT, MAP_LNG], MAP_ZOOM);

// Agregar capa de tiles (OpenStreetMap)
L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
  attribution: "© OpenStreetMap contributors",
}).addTo(map);

// Agregar marcador
L.marker([MAP_LAT, MAP_LNG])
  .addTo(map)
  .bindPopup("<h3>Ubicación del Evento</h3>")
  .openPopup();
