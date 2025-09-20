
let priceChartInstance = null;

//Funcion para cargar los precios, procentajes y volumen   
function cargarCriptomonedas() {
    axios.get('/Cryptolnvestment/public/getPrice')
        .then(function (response) {
            var data = response.data;

            var tableContent = `
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Símbolo</th>
                            <th>Precio (USD)</th>
                            <th>Cambio en 24h %</th>
                            <th>Volumen en 24h </th>
                        </tr>
                    </thead>
                    <tbody>`;

            data.forEach(function (crypto) {
                tableContent += `
                    <tr>
                        <td>${crypto.name}</td>
                        <td>${crypto.symbol}</td>
                        <td>$${crypto.price_usd.toFixed(2)}</td>
                        <td>${crypto.percent_change_24h.toFixed(2)}%</td>
                        <td>$${crypto.volume_24h.toFixed(2)}</td>
                    </tr>`;
            });

            tableContent += `</tbody></table>`;
            document.getElementById('tablaCriptos').innerHTML = tableContent;

            const nombres = data.map(crypto => crypto.name);
            const precios = data.map(crypto => crypto.price_usd);

            const ctx = document.getElementById('priceChart').getContext('2d');

// Destruye el grafico anterior si ya existe
if (priceChartInstance) {
    priceChartInstance.destroy();
}

//Se crea un nuevo grafico uy lo guarda en la variable
priceChartInstance = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: nombres,
        datasets: [{
            label: 'Precio USD',
            data: precios,
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            },
            title: {
                display: false
            }
        },
        scales: {
            x: {
                ticks: {
                    autoSkip: true,
                    maxRotation: 90,
                    minRotation: 45
                }
            },
            y: {
                beginAtZero: true
            }
        }
    }
});
        })
        .catch(function (error) {
            console.error(error);
            document.getElementById('result').innerHTML = `<div class="alert alert-danger">Error al obtener los datos.</div>`;
        });
}

document.addEventListener('DOMContentLoaded', function() {
    cargarCriptomonedas();
});


//Función para buscar una cripto en especifico
function buscarInformacionCripto() {

    var symbol = document.getElementById("txtIdCripto").value.trim().toUpperCase();

    if (!symbol) {
        alert("Ingresa un símbolo de criptomoneda: ");
        return;
    }

       axios.get(`/Cryptolnvestment/public/getInfo/${symbol}`)

        .then(function (response) {
            var data = response.data;

            console.log(response.data);
            var tableContent = `
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Categoria</th>
                    <th>Descripción</th>
                    <th>Fecha</th>
                    <th>Nombre</th>
                    <th>Simbolo</th>
                    <th>Website</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>${data.category}</td>
                    <td>${data.description}</td>
                    <td>${data.date_added}</td>
                    <td>${data.name}</td>
                    <td>${data.symbol}</td>
                    <td><a href="${data.website}" target="_blank">${data.website}</a></td>
                </tr>
            </tbody>
        </table>`;

            tableContent += `</tbody></table>`;
            document.getElementById('info').innerHTML = tableContent;
        })
        .catch(error => {
            console.error(error);

            document.getElementById('result').innerHTML = `
        <div class="alert alert-danger">No se encontraron datos para la criptomoneda con el id: "${symbol}".</div>`;
        });
}







