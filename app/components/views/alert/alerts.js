document.addEventListener('DOMContentLoaded', () => {
    const alertas = document.getElementById('alertas').value;

    mandarAlertas(alertas);
});

function mandarAlertas(alertas){
alertas = JSON.parse(alertas)
alertas.forEach(element => {
  notify(element.tipo, element.mensaje, element.tipo, 4000);
});

}
