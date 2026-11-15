# Aviatrax Studio - Gestion de Projets Musicaux

Application Laravel pour la gestion de projets musicaux avec suivi d'étapes de production et système de rôles sécurisé.

## 🎵 Fonctionnalités

### Rôles et Permissions
- **Administrateur** : Contrôle total, validation des projets et rôles
- **Secrétariat** : Gestion des comptes artistes et affectations
- **Responsable Studio** : Suivi et mise à jour des étapes de production
- **Artiste** : Visualisation de ses projets (lecture seule)

### Gestion des Projets
- Création et attribution de projets aux artistes
- Suivi d'étapes avec code couleur :
  - 🔵 Enregistrement (bleu)
  - 🟣 Mixage (rose)
  - 🟡 Mastering (jaune)
  - 🟠 Distribution en cours (orange)
  - 🟢 Distribution terminée (vert)
- Historique des changements d'étapes
- Notifications lors des mises à jour

### Tableaux de Bord Personnalisés
- Vue artiste : progression colorée et statistiques
- Vue studio : gestion complète des projets
- Vue secrétariat : gestion des artistes
- Vue admin : statistiques globales et validations

## 🚀 Installation

### Prérequis
- PHP 8.1+
- Composer
- MySQL 5.7+ ou MariaDB 10.2+

### Étapes d'installation

1. **Cloner le projet**
```bash
cd aviatrax-studio
```

2. **Installer les dépendances**
```bash
composer install
```

3. **Copier le fichier d'environnement**
```bash
cp .env.example .env
```

4. **Générer la clé d'application**
```bash
php artisan key:generate
```

5. **Configurer la base de données MySQL**
Modifier le fichier `.env` avec vos informations :
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aviator_music_studio
DB_USERNAME=root
DB_PASSWORD=<votre-mot-de-passe>
DB_CHARSET=utf8mb4
DB_COLLATION=utf8mb4_unicode_ci
```

6. **Créer la base de données MySQL**
```sql
CREATE DATABASE aviator_music_studio CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

7. **Exécuter les migrations**
```bash
php artisan migrate
```

8. **Seeder les données initiales**
```bash
php artisan db:seed
```

9. **Démarrer le serveur**
```bash
php artisan serve
```

## Compte administrateur initial

Configurez `ADMIN_EMAIL` et `ADMIN_PASSWORD` dans l’environnement avant d’exécuter les seeders. Aucun mot de passe administrateur par défaut n’est fourni. Les comptes de démonstration ne sont créés qu’en environnement local ou de test.

## 📁 Structure du Projet

```
app/
├── Models/
│   ├── User.php          # Modèle utilisateur avec rôles
│   ├── Role.php          # Modèle des rôles
│   ├── Projet.php        # Modèle des projets musicaux
│   └── ProjetEtape.php   # Modèle historique des étapes
├── Http/
│   └── Controllers/
│       ├── AuthController.php      # Authentification
│       ├── DashboardController.php # Tableaux de bord
│       ├── ProjetController.php    # Gestion des projets
│       └── AdminController.php     # Administration
├── Http/Middleware/
│   └── CheckRole.php     # Middleware de vérification des rôles
└── Policies/
    └── ProjetPolicy.php  # Permissions sur les projets

database/
├── migrations/           # Migrations de base de données
└── seeders/             # Seeders pour données initiales

resources/
└── views/
    ├── layouts/
    │   └── app.blade.php # Layout principal
    ├── auth/             # Vues d'authentification
    ├── dashboard/        # Tableaux de bord
    └── projets/          # Vues des projets
```

## 🔐 Sécurité

- Authentification sécurisée avec validation des comptes
- Middleware basé sur les rôles
- Journalisation (logs) des actions critiques
- Protection CSRF sur tous les formulaires
- Validation des données côté serveur

## 🎨 Interface

- Design moderne avec Tailwind CSS
- Interface responsive
- Code couleur pour les étapes de production
- Tableaux de bord personnalisés selon les rôles

## 📊 Logs

Les actions critiques sont journalisées dans `storage/logs/laravel.log` :
- Connexions/déconnexions
- Création/modification de projets
- Changements d'étapes
- Validation d'utilisateurs
- Modifications de rôles

## 🔧 Développement

### Ajouter un nouveau rôle
1. Ajouter le rôle dans `RoleSeeder.php`
2. Créer les méthodes correspondantes dans le modèle `User`
3. Ajouter les routes et middlewares nécessaires

### Ajouter une nouvelle étape
1. Modifier l'enum dans les migrations
2. Mettre à jour les modèles `Projet` et `ProjetEtape`
3. Ajouter la couleur dans le layout principal
4. Mettre à jour les vues

## 📝 Licence

Ce projet est développé pour Aviatrax Studio.

## 🆘 Support

Pour toute question ou problème, contactez l'équipe de développement. 