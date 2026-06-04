# iEVAL — Plateforme de gestion des évaluations en ligne

> Application web Laravel pour la création, la passation et l'évaluation de QCM en ligne, avec statistiques détaillées par étudiant et par chapitre.

**Projet de fin d'études — BTS Ibn Sina, Kénitra**
Filière : Développement des Applications Informatiques · Encadré par : **Hamid Alhaiane** (Professeur de Génie Logiciel)

---

## ✦ Aperçu

iEVAL est un système centralisé qui répond à trois rôles distincts :

| Rôle | Capacités principales |
|---|---|
| **Administrateur** | Gestion des utilisateurs, semestres, modules, chapitres et inscriptions |
| **Professeur** | Création/édition de QCM (QCU ou QCM multi-réponses), publication, suivi des tentatives, tableaux de bord statistiques (moyennes, distribution des notes, taux d'assimilation par chapitre) |
| **Étudiant** | Consultation des évaluations disponibles, passation chronométrée avec auto-soumission à l'expiration du temps, consultation des résultats avec correction |

---

## ✦ Stack technique

- **PHP 8.2+** · **Laravel 11**
- **MySQL** (développement local) · **PostgreSQL** (production / Supabase)
- **Tailwind CSS 3** + design system sur mesure (palette ink/paper/saffron/clay, typographies Fraunces + DM Sans)
- **Alpine.js** pour l'interactivité (formulaires de questions dynamiques, timer de passation)
- **Chart.js** pour les graphiques statistiques
- **Vite** pour le bundling frontend
- **Pest** / PHPUnit pour les tests · **Larastan** pour l'analyse statique · **Laravel Pint** pour le style de code

---

## ✦ Modèle de données

11 tables Eloquent reliées :

```
users (admin | teacher | student, matricule, is_active)
├─ taughtModules     ─→ modules.teacher_id
├─ enrolledModules   ─→ module_student (pivot)
├─ authoredEvaluations ─→ evaluations.teacher_id
└─ attempts          ─→ evaluation_attempts.student_id

semesters ─< modules ─< chapters
                    └< evaluations ─< questions ─< answer_options
                                  └< evaluation_attempts ─< student_answers
```

Caractéristiques notables du schéma :
- `evaluations` : status (`draft` / `published` / `closed`), fenêtre `opens_at`/`closes_at`, `duration_minutes`, `max_attempts`, `shuffle_questions`, `shuffle_options`
- `questions` : type `single` (QCU) ou `multiple` (QCM), rattachées à un **chapitre** pour permettre l'analyse d'assimilation
- `evaluation_attempts` : `score`, `max_score`, `grade_20`, status (`in_progress` / `submitted` / `auto_submitted`)
- `student_answers` : `selected_option_ids` en JSON, correction calculée à la soumission

---

## ✦ Installation

### Prérequis

- PHP 8.2 ou supérieur (`php -v`)
- Composer 2.x (`composer --version`)
- MySQL 8 (ou MariaDB 10.6+), ou SQLite pour un test rapide

### Étapes

```bash
# 1) Récupérer le projet
git clone https://github.com/<anas bougria>/iEVAL.git
cd iEVAL

# 2) Installer les dépendances PHP
composer install

# 3) Installer les dépendances JS
npm install

# 4) Préparer l'environnement
cp .env.example .env
php artisan key:generate

# 5) Configurer la base de données dans .env
#    DB_DATABASE=ieval, DB_USERNAME, DB_PASSWORD…
#    (ou DB_CONNECTION=sqlite + touch database/database.sqlite pour un test rapide)

# 6) Créer les tables et insérer les données 
php artisan migrate --seed

# 7) Compiler le frontend
npm run build    # production
# ou: npm run dev (mode développement avec hot-reload)

# 8) Lancer le serveur de développement
php artisan serve
# → http://localhost:8000
```

### Comptes de démonstration

Tous les mots de passe sont `********`.

| Rôle | Email |
|---|---|
| Administrateur | `admin@ieval.local` |
| Professeur (encadrant) | `h.alhaiane@ieval.local` |
| Professeur (démo) | `prof@ieval.local` |
| Étudiant | `etudiant@ieval.local` |

Le seeder crée également 3 autres professeurs et 14 autres étudiants, 4 modules (Programmation Web, Bases de Données, Algorithmique, Réseaux), avec chapitres, évaluations exemples et inscriptions.

---

## ✦ Structure du projet

```
iEVAL/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Auth, Dashboard, Admin/*, Teacher/*, Student/*
│   │   └── Middleware/        # EnsureRole
│   ├── Models/                # User, Semester, Module, Chapter,
│   │                            Evaluation, Question, AnswerOption,
│   │                            EvaluationAttempt, StudentAnswer
│   └── Providers/             # AppServiceProvider
├── bootstrap/
│   └── app.php                # Bootstrapping Laravel 11 (alias `role`)
├── config/                    # Configurations standards Laravel
├── database/
│   ├── migrations/            # 11 migrations (schéma complet)
│   └── seeders/               # Donnée de démonstration
├── public/                    # Point d'entrée web
├── resources/
│   ├── css/app.css            # Design system Tailwind académique
│   ├── js/                    # Alpine + Chart.js
│   └── views/
│       ├── layouts/app        # Layout principal (sidebar + topbar)
│       ├── partials/          # sidebar, topbar, flash
│       ├── auth/login         # Login éditorial 2-colonnes
│       ├── dashboard/         # Tableaux de bord par rôle
│       ├── admin/             # CRUD utilisateurs/semestres/modules/chapitres
│       ├── teacher/           # CRUD évaluations + questions + statistiques
│       └── student/           # Évaluations dispo + passation + résultats
├── routes/web.php             # Routage par rôle
└── tests/                     # Pest / PHPUnit
```

---

## ✦ Fonctionnalités clefs

### Création d'une évaluation (cas d'utilisation n°1)

1. Le professeur crée une évaluation (titre, module, durée, fenêtre d'ouverture, max. tentatives, mélange aléatoire des questions/options)
2. Il ajoute des questions (QCU / QCM multi-réponses), avec rattachement à un chapitre pour analyse fine
3. Chaque question définit ses options de réponse et marque celle(s) qui sont correctes
4. L'évaluation passe de **brouillon** à **publiée** quand prête (impossible si zéro question)

### Passation d'une évaluation (cas d'utilisation n°2)

1. L'étudiant voit la liste des évaluations ouvertes des modules auxquels il est inscrit
2. Le démarrage crée un `EvaluationAttempt` avec `started_at` ; un **timer côté serveur** (`duration_minutes`) garantit l'équité
3. L'interface chronométrée Alpine.js affiche le compte à rebours et alerte aux deux dernières minutes
4. La soumission peut être :
   - **manuelle** (`STATUS_SUBMITTED`) — le bouton de l'étudiant
   - **automatique** (`STATUS_AUTO_SUBMITTED`) — déclenchée par le serveur si le temps expire
5. La correction est calculée immédiatement : chaque réponse est `is_correct` si **et seulement si** l'ensemble sélectionné est strictement égal à l'ensemble des options correctes
6. Note ramenée sur 20 : `grade_20 = (score / max_score) * 20`

### Analyse pédagogique

Côté professeur, pour chaque évaluation :
- Distribution des notes sur 5 paliers (`[0-4[`, `[4-8[`, `[8-12[`, `[12-16[`, `[16-20]`)
- Moyenne, médiane, min/max
- **Taux d'assimilation par chapitre** : `% bonnes réponses` pour les questions rattachées à chaque chapitre — c'est l'indicateur qui permet d'identifier les concepts mal compris

---

## ✦ Méthodologie : RUP

Le projet suit le **Rational Unified Process** en quatre phases itératives :

1. **Inception** — Définition des besoins, cas d'utilisation, environnement technique
2. **Élaboration** — Architecture, modélisation UML (cas d'utilisation, classes, séquence)
3. **Construction** — Implémentation incrémentale (cas d'utilisation 1 puis 2 puis fonctionnalités secondaires)
4. **Transition** — Tests utilisateurs, validation, déploiement Supabase

### Jalons

| Date | Livrable |
|---|---|
| 13/11/2025 | Remise officielle du sujet |
| 11/12/2025 | Dossier d'étude du domaine |
| 12/01/2026 | Analyse & conception du cas d'utilisation **créer une évaluation** |
| 12/02/2026 | Implémentation/test/déploiement du CU 1 |
| 09/03/2026 | Cycle complet du CU **passer une évaluation** |
| 07/05/2026 | Livraison version finale |
| 11/06/2026 | Remise du rapport avant impression et soutenance |

---

## ✦ Commandes utiles

```bash
# Migrations
php artisan migrate                # Appliquer
php artisan migrate:refresh --seed # Tout réinitialiser + seeder
php artisan db:seed                # Seeder uniquement

# Cache & config (utile en cas de modification d'.env)
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Qualité du code
./vendor/bin/pint                  # Auto-formatage PSR-12
./vendor/bin/phpstan analyse       # Analyse statique (Larastan)
php artisan test                   # Tests Pest


```

---

## ✦ Déploiement en production (Supabase / PostgreSQL)

1. Créer un projet Supabase, récupérer les coordonnées PostgreSQL
2. Configurer `.env` :
   ```dotenv
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://votre-domaine

   DB_CONNECTION=pgsql
   DB_HOST=db.xxxx.supabase.co
   DB_PORT=5432
   DB_DATABASE=postgres
   DB_USERNAME=postgres
   DB_PASSWORD=...
   DB_SSLMODE=require
   ```
3. `composer install --no-dev --optimize-autoloader`
4. `npm run build`
5. `php artisan migrate --force`
6. `php artisan config:cache && php artisan route:cache && php artisan view:cache`
7. Pointer le serveur web vers le dossier `public/`

---

## ✦ Licence & remerciements

Projet académique réalisé dans le cadre du BTS Développement des Applications Informatiques, **BTS Ibn Sina – Kénitra**, 2025-2026.

Encadré par **M. Hamid Alhaiane**, Professeur de Génie Logiciel.

Le code est sous licence MIT pour permettre la réutilisation pédagogique.
