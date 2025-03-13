# Application de Liste de Tâches (TaskMaster)

Une application web moderne de gestion de tâches avec une interface utilisateur élégante, développée avec Laravel 11 et Vue.js.

## Caractéristiques

- Interface utilisateur moderne et réactive
- Gestion complète des tâches (CRUD)
- Filtrage des tâches par statut et priorité
- Système de statut et priorité visuellement différenciés
- Dates d'échéance pour les tâches
- Design responsive adapté à tous les appareils

## Technologies utilisées

- **Backend**: Laravel 11
- **Frontend**: Vue.js
- **Base de données**: MySQL
- **Styles**: CSS personnalisé avec variables
- **CI/CD**: GitHub Actions
- **Containerisation**: Docker

## Installation et configuration

### Prérequis

- PHP 8.3+
- Composer
- Node.js et NPM
- MySQL ou SQLite

### Installation

1. Cloner le dépôt
   ```bash
   git clone https://github.com/votre-utilisateur/todo-app.git
   cd todo-app
   ```

2. Installer les dépendances PHP
   ```bash
   composer install
   ```

3. Installer les dépendances JavaScript
   ```bash
   npm install
   ```

4. Copier le fichier d'environnement
   ```bash
   cp .env.example .env
   ```

5. Configurer la base de données dans le fichier `.env`
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=todo_app
   DB_USERNAME=root
   DB_PASSWORD=
   ```

6. Générer la clé d'application
   ```bash
   php artisan key:generate
   ```

7. Exécuter les migrations
   ```bash
   php artisan migrate
   ```

8. Compiler les assets
   ```bash
   npm run dev
   ```

9. Démarrer le serveur
   ```bash
   php artisan serve
   ```

L'application sera accessible à l'adresse http://localhost:8000

## Structure du projet

- `app/Models/Task.php` - Modèle Eloquent pour les tâches
- `app/Http/Controllers/Api/TaskController.php` - Contrôleur API pour la gestion des tâches
- `resources/js/components/` - Composants Vue.js
- `resources/js/views/` - Vues Vue.js
- `resources/js/services/` - Services JavaScript pour les appels API

## Pipeline CI/CD

Ce projet utilise GitHub Actions pour exécuter les tests automatisés et déployer l'application.

1. **Tests**:
    - Exécution des tests unitaires et d'intégration
    - Analyse de code PHPStan

2. **Build et Push**:
    - Construction de l'image Docker
    - Push vers DockerHub

3. **Déploiement**:
    - Déploiement sur le serveur de staging via SSH
    - Mise à jour de l'application avec les nouvelles modifications

## Captures d'écran

*Captures d'écran à ajouter une fois le projet terminé*

## Équipe

*Liste des membres de l'équipe à ajouter*

## Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.
