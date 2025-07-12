# Application Météo PHP

Cette application météo permet d'afficher la météo en temps réel pour n'importe quelle ville, avec un graphique des températures horaires et une photo de la ville en fond, grâce à l'API WeatherAPI et l'API Pixabay.

## Fonctionnalités

- Recherche météo par ville (saisie utilisateur)
- Affichage des conditions météo actuelles (température, ressenti, humidité, vent, pression)
- Graphique des températures horaires (Chart.js)
- Photo de la ville en fond du graphique (Pixabay)
- Responsive et design moderne
- Gestion des erreurs (ville inconnue, API, etc.)

## Technologies utilisées

- **PHP** (backend)
- **HTML/CSS** (frontend)
- **Chart.js** (graphique)
- **WeatherAPI** (données météo)
- **Pixabay API** (photos de ville)

## Installation

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/TON_UTILISATEUR/NOM_DU_DEPOT.git
   cd NOM_DU_DEPOT
   ```
2. **Placer les fichiers sur un serveur web local** (XAMPP, WAMP, MAMP, ou serveur PHP intégré)
   ```bash
   php -S localhost:8000
   ```
3. **Ouvrir dans le navigateur**
   - Aller sur [http://localhost:8000](http://localhost:8000)

## Configuration

- **Clé API WeatherAPI** : à renseigner dans `config.php`
- **Clé API Pixabay** : déjà intégrée dans le code (remplacez-la par la vôtre si besoin)

## Publication sur GitHub

1. **Initialiser le dépôt (si ce n'est pas déjà fait)**
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   ```
2. **Créer un dépôt sur GitHub**
3. **Ajouter le dépôt distant**
   ```bash
   git remote add origin https://github.com/TON_UTILISATEUR/NOM_DU_DEPOT.git
   ```
4. **Pousser le code**
   ```bash
   git push -u origin master
   ```

## Personnalisation

- Modifier le style dans `style.css`
- Changer la logique d'affichage des photos dans `index.php`
- Ajouter d'autres sources d'images si besoin

## Crédits

- Données météo : [WeatherAPI](https://www.weatherapi.com/)
- Photos de ville : [Pixabay](https://pixabay.com/)
- Graphiques : [Chart.js](https://www.chartjs.org/)

## Licence

Projet libre pour usage personnel et éducatif. 