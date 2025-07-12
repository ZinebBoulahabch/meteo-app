// Configuration de l'API
const API_KEY = '3b9e3e0d8f144a3884200904251207'; // Remplacez par votre clé API WeatherAPI.com
const BASE_URL = 'https://api.weatherapi.com/v1/forecast.json';

// Éléments DOM
const cityInput = document.getElementById('cityInput');
const searchBtn = document.getElementById('searchBtn');
const weatherInfo = document.getElementById('weatherInfo');
const loading = document.getElementById('loading');
const error = document.getElementById('error');
const errorMessage = document.getElementById('errorMessage');

// Événements
searchBtn.addEventListener('click', getWeather);
cityInput.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') {
        getWeather();
    }
});

// Fonction principale pour récupérer la météo
async function getWeather() {
    const city = cityInput.value.trim();
    
    if (!city) {
        showError('Veuillez entrer le nom d\'une ville');
        return;
    }

    showLoading();
    hideError();

    try {
        const response = await fetch(`${BASE_URL}?key=${API_KEY}&q=${encodeURIComponent(city)}&lang=fr&hours=12`);
        
        if (!response.ok) {
            if (response.status === 400) {
                throw new Error('Ville non trouvée. Vérifiez l\'orthographe.');
            } else {
                throw new Error('Erreur lors de la récupération des données météo.');
            }
        }

        const data = await response.json();
        displayWeather(data);
    } catch (err) {
        showError(err.message);
    }
}

// Fonction pour afficher les données météo (adaptée à WeatherAPI)
function displayWeather(data) {
    const iconUrl = 'https:' + data.current.condition.icon;
    const weatherData = `
        <div class="weather-data">
            <div class="city-name">${data.location.name}, ${data.location.country}</div>
            <div class="temperature">${Math.round(data.current.temp_c)}°C</div>
            <div class="weather-icon">
                <img src="${iconUrl}" alt="${data.current.condition.text}">
            </div>
            <div class="description">${data.current.condition.text}</div>
            <div class="weather-details">
                <div class="detail-item">
                    <i class="fas fa-thermometer-half"></i>
                    <div class="detail-label">Ressenti</div>
                    <div class="detail-value">${Math.round(data.current.feelslike_c)}°C</div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-tint"></i>
                    <div class="detail-label">Humidité</div>
                    <div class="detail-value">${data.current.humidity}%</div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-wind"></i>
                    <div class="detail-label">Vent</div>
                    <div class="detail-value">${Math.round(data.current.wind_kph)} km/h</div>
                </div>
                <div class="detail-item">
                    <i class="fas fa-compress-alt"></i>
                    <div class="detail-label">Pression</div>
                    <div class="detail-value">${data.current.pressure_mb} hPa</div>
                </div>
            </div>
        </div>
    `;
    weatherInfo.innerHTML = weatherData;
    // Affichage des prévisions horaires
    if (data.forecast && data.forecast.forecastday && data.forecast.forecastday[0] && data.forecast.forecastday[0].hour) {
        const hours = data.forecast.forecastday[0].hour;
        // Par défaut, sélectionne l'heure actuelle
        const now = new Date();
        const currentHour = now.getHours();
        // Afficher 5 points espacés de 3 heures à partir de l'heure actuelle
        const interval = 3;
        let selectedHours = [];
        let startIdx = hours.findIndex(h => parseInt(h.time.split(' ')[1].slice(0,2)) === currentHour);
        if (startIdx === -1) startIdx = 0;
        for (let i = 0; i < 5; i++) {
            const idx = startIdx + i * interval;
            if (idx < hours.length) {
                selectedHours.push(hours[idx]);
            }
        }
        renderHourlyChart(selectedHours);
    }
    hideLoading();
}

function renderHourlyChart(hours) {
    const ctx = document.getElementById('hourlyChart').getContext('2d');
    // Détruit l'ancien graphique s'il existe
    if (window.hourlyChartInstance) {
        window.hourlyChartInstance.destroy();
    }
    const labels = hours.map(hour => hour.time.split(' ')[1].slice(0,5));
    const dataTemps = hours.map(hour => hour.temp_c);
    const minTemp = Math.min(...dataTemps);
    const maxTemp = Math.max(...dataTemps);

    window.hourlyChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Température (°C)',
                data: dataTemps,
                fill: true,
                backgroundColor: 'rgba(255, 205, 86, 0.2)',
                borderColor: 'rgba(255, 205, 86, 1)',
                borderWidth: 2,
                pointRadius: 2,
                tension: 0.4
            }]
        },
        options: {
            plugins: {
                legend: { display: false },
                datalabels: {
                    color: '#222', // plus foncé pour la lisibilité
                    anchor: 'end',
                    align: 'top',
                    font: { weight: 'bold', size: 14 },
                    formatter: function(value) {
                        return value + '°';
                    },
                    clip: false, // Affiche les labels même sur les bords
                    display: true
                }
            },
            scales: {
                x: {
                    display: true,
                    title: { display: false },
                    ticks: {
                        color: '#222', // couleur foncée pour les heures
                        font: { weight: 'bold', size: 13 }
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                y: {
                    display: false,
                    min: Math.floor(minTemp) - 1, // marge basse
                    max: Math.ceil(maxTemp) + 1   // marge haute
                }
            }
        },
        plugins: [ChartDataLabels]
    });
}

// Fonctions utilitaires
function showLoading() {
    loading.style.display = 'block';
    weatherInfo.style.display = 'none';
    error.style.display = 'none';
}

function hideLoading() {
    loading.style.display = 'none';
    weatherInfo.style.display = 'block';
}

function showError(message) {
    errorMessage.textContent = message;
    error.style.display = 'block';
    weatherInfo.style.display = 'none';
    loading.style.display = 'none';
}

function hideError() {
    error.style.display = 'none';
}

// Message d'information pour l'utilisateur
console.log('Pour utiliser cette application, vous devez obtenir une clé API gratuite sur https://www.weatherapi.com/');
console.log('Remplacez TA_CLE_WEATHERAPI_ICI dans le fichier script.js par votre clé API WeatherAPI.com'); 