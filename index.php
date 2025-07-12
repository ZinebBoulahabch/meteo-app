<?php
require_once 'config.php';
// Intégration Pixabay améliorée
$backgroundUrl = '';
$creditHtml = '';
if (isset($_POST['city']) && !empty($_POST['city'])) {
    $city = trim($_POST['city']);
    $searchQuery = $city;
    // Si on a déjà les données météo, on ajoute le pays pour plus de précision
    if (isset($weatherData['location']['country'])) {
        $searchQuery .= ' ' . $weatherData['location']['country'];
    }
    $searchQuery = urlencode($searchQuery);
    $pixabayApiKey = '51288832-9b500b7adcf18d6466b381454';
    $pixabayUrl = "https://pixabay.com/api/?key=$pixabayApiKey&q=$searchQuery&image_type=photo&category=places&per_page=10";
    $pixabayResponse = @file_get_contents($pixabayUrl);
    $cityLower = strtolower(trim($_POST['city']));
    if ($pixabayResponse !== false) {
        $pixabayData = json_decode($pixabayResponse, true);
        $found = false;
        if (!empty($pixabayData['hits'])) {
            // Filtrer pour trouver une image dont les tags ou le titre contiennent le nom de la ville
            foreach ($pixabayData['hits'] as $hit) {
                $tags = strtolower($hit['tags']);
                $title = strtolower($hit['user']); // Pixabay n'a pas de titre, on peut utiliser tags
                if (strpos($tags, $cityLower) !== false || strpos($title, $cityLower) !== false) {
                    $backgroundUrl = $hit['webformatURL'];
                    $creditHtml = '<div style="font-size:12px;text-align:right;color:#555;">Photo: <a href="'.htmlspecialchars($hit['pageURL']).'" target="_blank" style="color:#555;text-decoration:underline;">'.htmlspecialchars($hit['user']).' / Pixabay</a></div>';
                    $found = true;
                    break;
                }
            }
            // Si aucune image très pertinente, prendre la première
            if (!$found) {
                $backgroundUrl = $pixabayData['hits'][0]['webformatURL'];
                $creditHtml = '<div style="font-size:12px;text-align:right;color:#555;">Photo: <a href="'.htmlspecialchars($pixabayData['hits'][0]['pageURL']).'" target="_blank" style="color:#555;text-decoration:underline;">'.htmlspecialchars($pixabayData['hits'][0]['user']).' / Pixabay</a></div>';
            }
        }
    }
    // Fallback si aucune image trouvée : image générique de ville
    if (!$backgroundUrl) {
        $backgroundUrl = 'https://cdn.pixabay.com/photo/2016/11/29/09/32/architecture-1868667_960_720.jpg'; // image générique "city"
        $creditHtml = '<div style="font-size:12px;text-align:right;color:#555;">Photo: <a href="https://pixabay.com/photos/architecture-building-city-1868667/" target="_blank" style="color:#555;text-decoration:underline;">Pixabay</a></div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Météo en Temps Réel</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
</head>
<body>
    <div class="container">
        <div class="weather-card">
            <div class="search-section">
                <h1><i class="fas fa-cloud-sun"></i> Météo en Temps Réel</h1>
                <form method="POST" action="" id="weatherForm">
                    <div class="search-box">
                        <input type="text" name="city" id="cityInput" placeholder="Entrez le nom d'une ville..." autocomplete="off" value="<?php echo isset($_POST['city']) ? htmlspecialchars($_POST['city']) : ''; ?>">
                        <button type="submit" id="searchBtn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="weather-info" id="weatherInfo">
                <?php
                if (isset($_POST['city']) && !empty($_POST['city'])) {
                    // Affiche le bloc météo généré par weather.php
                    include 'weather.php';
                } else {
                    // Message d'accueil
                    echo '<div class="welcome-message">
                        <i class="fas fa-map-marker-alt"></i>
                        <h2>Recherchez une ville</h2>
                        <p>Entrez le nom d\'une ville pour voir la météo actuelle</p>
                    </div>';
                }
                ?>
            </div>
        </div>
    </div>
    <div class="chart-container" style="width:100%;max-width:900px;margin:30px auto;<?php if($backgroundUrl) echo "background:url('$backgroundUrl') center/cover no-repeat;" ?>">
        <canvas id="hourlyChart"></canvas>
        <?php echo $creditHtml; ?>
    </div>
    <script>
        // Chart.js et gestion des erreurs
        <?php if (isset($chartData) && $chartData && isset($_POST['city']) && !$error): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const hours = <?php echo json_encode($chartData); ?>;
            renderHourlyChart(hours);
        });
        <?php endif; ?>
        function renderHourlyChart(hours) {
            const ctx = document.getElementById('hourlyChart').getContext('2d');
            if (window.hourlyChartInstance) {
                window.hourlyChartInstance.destroy();
            }
            const labels = hours.map(hour => hour.time);
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
                            color: '#222',
                            anchor: 'end',
                            align: 'top',
                            font: { weight: 'bold', size: 14 },
                            formatter: function(value) {
                                return value + '°';
                            },
                            clip: false,
                            display: true
                        }
                    },
                    scales: {
                        x: {
                            display: true,
                            title: { display: false },
                            ticks: {
                                color: '#222',
                                font: { weight: 'bold', size: 13 }
                            },
                            grid: { color: 'rgba(0,0,0,0.05)' }
                        },
                        y: {
                            display: false,
                            min: Math.floor(minTemp) - 1,
                            max: Math.ceil(maxTemp) + 1
                        }
                    }
                },
                plugins: [ChartDataLabels]
            });
        }
    </script>
</body>
</html> 