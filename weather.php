<?php
require_once 'config.php';

$error = null;
$weatherData = null;
$chartData = [];

if (isset($_POST['city']) && !empty($_POST['city'])) {
    $city = trim($_POST['city']);
    $url = WEATHER_API_BASE_URL . '?key=' . WEATHER_API_KEY . '&q=' . urlencode($city) . '&lang=fr&hours=24';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($response === false) {
        $error = 'Erreur de connexion à l\'API météo.';
    } elseif ($httpCode !== 200) {
        if ($httpCode === 400) {
            $error = 'Ville non trouvée. Vérifiez l\'orthographe.';
        } else {
            $error = 'Erreur lors de la récupération des données météo.';
        }
    } else {
        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = 'Erreur lors du traitement des données.';
        } elseif (isset($data['error'])) {
            $error = $data['error']['message'] ?? 'Erreur de l\'API météo.';
        } else {
            $weatherData = $data;
            // Préparation des données pour le graphique
            if (isset($data['forecast']['forecastday'][0]['hour'])) {
                $hours = $data['forecast']['forecastday'][0]['hour'];
                // Si besoin, on ajoute les heures du lendemain pour boucler
                if (isset($data['forecast']['forecastday'][1]['hour'])) {
                    $hours = array_merge($hours, $data['forecast']['forecastday'][1]['hour']);
                }
                $now = new DateTime();
                $currentHour = (int)$now->format('H');
                // Trouver l'index de l'heure la plus proche de l'heure actuelle
                $closestIdx = 0;
                $minDiff = 24;
                foreach ($hours as $index => $hour) {
                    $hourTime = new DateTime($hour['time']);
                    $diff = abs((int)$hourTime->format('H') - $currentHour);
                    if ($diff < $minDiff) {
                        $minDiff = $diff;
                        $closestIdx = $index;
                    }
                }
                // Sélectionner 5 points espacés de 3h, en bouclant si besoin
                $interval = 3;
                $selectedHours = [];
                $totalHours = count($hours);
                for ($i = 0; $i < 5; $i++) {
                    $idx = $closestIdx + ($i * $interval);
                    if ($idx >= $totalHours) {
                        $idx = $idx % $totalHours;
                    }
                    $hourTime = new DateTime($hours[$idx]['time']);
                    $selectedHours[] = [
                        'time' => $hourTime->format('H:i'),
                        'temp_c' => $hours[$idx]['temp_c']
                    ];
                }
                $chartData = $selectedHours;
            }
        }
    }
}

if ($weatherData && !$error): ?>
    <div class="weather-data">
        <div class="city-name"><?php echo htmlspecialchars($weatherData['location']['name']); ?>, <?php echo htmlspecialchars($weatherData['location']['country']); ?></div>
        <div class="temperature"><?php echo round($weatherData['current']['temp_c']); ?>°C</div>
        <div class="weather-icon">
            <img src="https:<?php echo htmlspecialchars($weatherData['current']['condition']['icon']); ?>" alt="<?php echo htmlspecialchars($weatherData['current']['condition']['text']); ?>">
        </div>
        <div class="description"><?php echo htmlspecialchars($weatherData['current']['condition']['text']); ?></div>
        <div class="weather-details">
            <div class="detail-item">
                <i class="fas fa-thermometer-half"></i>
                <div class="detail-label">Ressenti</div>
                <div class="detail-value"><?php echo round($weatherData['current']['feelslike_c']); ?>°C</div>
            </div>
            <div class="detail-item">
                <i class="fas fa-tint"></i>
                <div class="detail-label">Humidité</div>
                <div class="detail-value"><?php echo $weatherData['current']['humidity']; ?>%</div>
            </div>
            <div class="detail-item">
                <i class="fas fa-wind"></i>
                <div class="detail-label">Vent</div>
                <div class="detail-value"><?php echo round($weatherData['current']['wind_kph']); ?> km/h</div>
            </div>
            <div class="detail-item">
                <i class="fas fa-compress-alt"></i>
                <div class="detail-label">Pression</div>
                <div class="detail-value"><?php echo $weatherData['current']['pressure_mb']; ?> hPa</div>
            </div>
        </div>
    </div>
    <script>
        // Envoi des données horaires à Chart.js
        document.addEventListener('DOMContentLoaded', function() {
            const hours = <?php echo json_encode($chartData); ?>;
            if (hours && hours.length > 0 && typeof renderHourlyChart === 'function') {
                renderHourlyChart(hours);
            }
        });
    </script>
<?php elseif ($error): ?>
    <div class="error">
        <i class="fas fa-exclamation-triangle"></i>
        <p><?php echo htmlspecialchars($error); ?></p>
    </div>
<?php endif; ?> 