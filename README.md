# Portail Éducation API

## Description du Projet

Ce projet est une API RESTful développée avec Laravel, conçue pour gérer les données d'un portail éducatif. Elle permet la gestion des années académiques, des classes, des étudiants, des enseignants, des matières, des notes, des bulletins de notes, et d'autres entités liées à un système scolaire. L'API intègre des fonctionnalités avancées telles que la génération asynchrone de bulletins de notes au format PDF et le calcul des classements des étudiants.

## Fonctionnalités

*   **Gestion des Utilisateurs et Rôles**: Authentification, gestion des profils (étudiants, parents, enseignants, administrateurs).
*   **Gestion Académique**:
    *   Années académiques et trimestres/semestres.
    *   Classes et sessions d'étudiants.
    *   Matières et coefficients.
*   **Gestion des Notes et Évaluations**:
    *   Enregistrement des notes par matière et par trimestre.
    *   Calcul des moyennes générales et par matière.
*   **Génération de Bulletins de Notes**:
    *   Génération asynchrone de bulletins de notes au format PDF.
    *   Calcul automatique des classements des étudiants.
    *   Stockage des PDF générés.
*   **Affectations (Assignments)**: Liaison entre enseignants, classes, matières et trimestres.
*   **API RESTful**: Points de terminaison clairs et bien définis pour toutes les opérations CRUD.
*   **Système de File d'Attente (Queue)**: Utilisation de queues pour les tâches de fond (génération de PDF, calculs complexes) afin d'améliorer la réactivité de l'API.

## Technologies Utilisées

*   **PHP**: Langage de programmation principal.
*   **Laravel Framework**: Cadre de développement web pour PHP.
*   **Composer**: Gestionnaire de dépendances pour PHP.
*   **Node.js & npm/Yarn**: Pour les dépendances frontend (Vite) et la compilation des assets.
*   **SQLite**: Base de données par défaut pour le développement (peut être configurée pour MySQL, PostgreSQL, etc.).
*   **DomPDF**: Bibliothèque pour la génération de PDF.
*   **Laravel Queue**: Pour la gestion des tâches asynchrones.

## Prérequis

Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre machine :

*   PHP >= 8.2
*   Composer
*   Node.js >= 18
*   npm ou Yarn
*   Un serveur web (Apache, Nginx) ou le serveur de développement intégré de Laravel.
*   SQLite (généralement inclus avec PHP, ou un autre SGBD comme MySQL/PostgreSQL).

## Installation

Suivez ces étapes pour configurer le projet localement :

1.  **Cloner le dépôt** :
    ```bash
    git clone https://github.com/votre-utilisateur/portailEducationApi.git
    cd portailEducationApi
    ```

2.  **Installer les dépendances PHP** :
    ```bash
    composer install
    ```

3.  **Installer les dépendances JavaScript** :
    ```bash
    npm install # ou yarn install
    ```

4.  **Configurer l'environnement** :
    Copiez le fichier `.env.example` et renommez-le en `.env`.
    ```bash
    cp .env .env
    ```
    Générez une clé d'application :
    ```bash
    php artisan key:generate
    ```
    Assurez-vous que votre fichier `.env` contient les configurations de base de données appropriées. Par défaut, SQLite est configuré. Si vous utilisez SQLite, assurez-vous que le fichier `database/database.sqlite` existe (vous pouvez le créer avec `touch database/database.sqlite`).

5.  **Exécuter les migrations de base de données et les seeders** :
    ```bash
    php artisan migrate:fresh --seed
    ```
    Cette commande va créer les tables de la base de données et les remplir avec des données de test.

6.  **Lier le stockage public** :
    ```bash
    php artisan storage:link
    ```
    Ceci est nécessaire pour que les PDF générés soient accessibles via une URL publique.

7.  **Compiler les assets frontend** (si nécessaire, pour les vues Blade utilisées par DomPDF) :
    ```bash
    npm run build # ou yarn build
    ```

8.  **Démarrer le serveur de développement Laravel** :
    ```bash
    php artisan serve
    ```
    L'API sera accessible à `http://127.0.0.1:8000` (ou un autre port).

9.  **Démarrer le gestionnaire de file d'attente (Queue Worker)** :
    Pour que les tâches asynchrones (génération de PDF, calcul de rang) fonctionnent, vous devez démarrer un worker de file d'attente.
    ```bash
    php artisan queue:work
    ```
    Il est recommandé d'utiliser un outil comme Supervisor en production pour gérer ce processus.

## Utilisation

Une fois l'API installée et le serveur démarré, vous pouvez interagir avec elle via des requêtes HTTP (GET, POST, PUT, DELETE).

**Exemples de points de terminaison (à titre indicatif) :**

*   `GET /api/report-cards` : Récupérer tous les bulletins de notes.
*   `POST /api/report-cards/generate` : Générer des bulletins de notes pour une classe et un trimestre donnés.
    *   Corps de la requête (JSON) : `{"class_model_id": 1, "term_id": 1}`

Consultez le fichier `routes/api.php` pour la liste complète des points de terminaison disponibles.

## Structure du Projet

Le projet suit la structure standard de Laravel, avec des modules organisés pour une meilleure maintenabilité :

*   `app/Http/Controllers`: Contrôleurs HTTP.
*   `app/Jobs`: Tâches de fond (ex: `GenerateReportCardPdfJob`, `CalculateReportCardRanksJob`).
*   `app/Models`: Modèles Eloquent.
*   `app/Modules`: Contient les modules spécifiques à l'application (AcademicYear, Assignement, ClassModel, Grade, Parent, ReportCard, Student, Subject, Teacher, Term, User), chacun avec ses propres Controllers, Models, Requests, Ressources et Services.
*   `database/migrations`: Migrations de base de données.
*   `database/seeders`: Seeders pour peupler la base de données avec des données de test.
*   `resources/views/reports`: Vues Blade utilisées pour la génération de PDF (ex: `bulletin.blade.php`).
*   `routes/api.php`: Définition des routes de l'API.
*   `storage/app/public/report_cards`: Emplacement où les PDF des bulletins sont stockés.

## Tests

Pour exécuter les tests unitaires et fonctionnels :

```bash
php artisan test
```

## Contribution

Les contributions sont les bienvenues ! Veuillez suivre les étapes suivantes :

1.  Fork le dépôt.
2.  Créez une nouvelle branche (`git checkout -b feature/ma-nouvelle-fonctionnalite`).
3.  Effectuez vos modifications.
4.  Assurez-vous que les tests passent.
5.  Commitez vos changements (`git commit -am 'feat: ajouter ma nouvelle fonctionnalité'`).
6.  Poussez vers la branche (`git push origin feature/ma-nouvelle-fonctionnalite`).
7.  Créez une Pull Request.

## Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.
