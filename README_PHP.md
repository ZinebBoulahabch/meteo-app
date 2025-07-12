# Application Météo en PHP

Cette application météo a été convertie de JavaScript vers PHP. Elle utilise l'API WeatherAPI.com pour récupérer les données météorologiques en temps réel.

## Fonctionnalités

- ✅ Recherche de météo par ville
- ✅ Affichage de la température actuelle
- ✅ Conditions météorologiques avec icônes
- ✅ Détails : ressenti, humidité, vent, pression
- ✅ Graphique des températures horaires
- ✅ Interface responsive et moderne
- ✅ Gestion des erreurs

## Prérequis

- Serveur web avec PHP (Apache, Nginx, etc.)
- Extension PHP cURL activée
- Clé API WeatherAPI.com (gratuite)

## Installation

1. **Téléchargez les fichiers** dans votre dossier web
2. **Obtenez une clé API** sur [WeatherAPI.com](https://www.weatherapi.com/)
3. **Modifiez `config.php`** et remplacez la clé API :
   ```php
   define('WEATHER_API_KEY', 'VOTRE_CLE_API_ICI');
   ```

## Structure des fichiers

```
METEO/
├── index.php          # Page principale avec formulaire
├── weather.php        # Script PHP pour l'API
├── config.php         # Configuration (clé API)
├── style.css          # Styles CSS
└── README_PHP.md      # Ce fichier
```

## Utilisation

1. Ouvrez `index.php` dans votre navigateur
2. Entrez le nom d'une ville
3. Cliquez sur le bouton de recherche
4. Consultez les données météo et le graphique

## Différences avec la version JavaScript

### Avantages de la version PHP :
- ✅ Traitement côté serveur (plus sécurisé)
- ✅ Pas d'exposition de la clé API côté client
- ✅ Meilleure compatibilité navigateur
- ✅ Possibilité d'ajouter une base de données
- ✅ Cache côté serveur possible

### Fonctionnalités conservées :
- ✅ Même interface utilisateur
- ✅ Même design et animations
- ✅ Graphique Chart.js
- ✅ Gestion des erreurs
- ✅ Responsive design

## Configuration serveur

### Apache (.htaccess)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

### Nginx
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## Sécurité

- ✅ Protection contre les injections XSS avec `htmlspecialchars()`
- ✅ Validation des données d'entrée
- ✅ Gestion des erreurs API
- ✅ Clé API sécurisée côté serveur

## Personnalisation

### Modifier le style
Éditez `style.css` pour changer l'apparence.

### Ajouter des fonctionnalités
- Base de données pour sauvegarder les recherches
- Cache des données météo
- Prévisions sur plusieurs jours
- Géolocalisation automatique

## Support

Pour toute question ou problème :
1. Vérifiez que PHP cURL est activé
2. Vérifiez votre clé API WeatherAPI.com
3. Consultez les logs d'erreur du serveur

## Licence

Libre d'utilisation pour projets personnels et éducatifs. 