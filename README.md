# LearningShare

## peut ê nécessaire : installer l'utilitaire en ligne de commande symfony

les instuctions d'instalation sont ici <https://symfony.com/download>
symfony new tpStage --version="6.4.\*" --webapp

## instuctions pour le premier lancement du serv de dev avec docker

il faut activer les extensions pgsql de votre php dans le php.ini
si des pb surviennent peut être pertinent d'utiliser ca :

```
docker-compose build --no-cache
docker-compose up -d
```

# a ne lancer qu'une seule fois

1. au cas ou lancer cette commande si apache2 occupe déja l'adresse localhost

```
sudo systemctl stop apache2
```

2. ensuite se rendre dans le dossier "symfony-docker"

> les commandes docker auront peut être besoin de sudo

3. suivre ces instructions :

    If not already done, install Docker Compose (v2.10+)

    Run to build fresh images :

```
docker compose build --no-cache
```

Run to set up and start a fresh Symfony project :

```
docker compose up --pull always -d --wait
```

Open <https://localhost> in your favorite web browser and accept the auto-generated TLS certificate

Run to stop the Docker containers :

```
docker compose down --remove-orphans
```

## instuctions pour lancer/fermer le docker (serv de dev) après la première installation

1. lancer le docker
   `sudo docker compose up -d`
2. fermer le docker
   `sudo docker compose down --remove-orphans`

## instructions pour initier le hot-reload pnd le dev

1. installer browser-sync de manière globale avec npm

```
npm install -g browser-sync
```

2. a la racine du dossier lancer cette commande :

```
browser-sync start --proxy "https://localhost:443" --files "**/*.php, **/*.css, **/*.js, **/*.html, **/*.twig, **/*.yaml, **/*.env, var/cache/**/*, var/logs/**/*" --https --no-inject-changes
```

## créer la base de données

Steps to Set Up PostgreSQL User with Full Permissions

    Connect to PostgreSQL as Superuser:
    Open your terminal and connect to PostgreSQL using the postgres superuser:

bash

sudo -u postgres psql

Create the User (if not already created):
Create a new user (role) named app with a password:

sql

CREATE ROLE app WITH LOGIN PASSWORD 'your_password';

Replace 'your_password' with a secure password.

Grant CREATEDB Privilege:
Allow the app user to create databases:

sql

ALTER ROLE app CREATEDB;

Create the Database (if not already created):
Create the database named app:

sql

CREATE DATABASE app;

Grant All Privileges on the Database:
Grant all privileges on the app database to the app user:

sql

GRANT ALL PRIVILEGES ON DATABASE app TO app;

Grant Privileges on All Tables:
Connect to the app database and grant privileges on all existing tables and sequences:

sql

\c app -- Connect to the app database
GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO app;
GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO app;

Set Default Privileges:
Ensure the app user has privileges on any new tables or sequences created in the future:

sql

ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON TABLES TO app;
ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL PRIVILEGES ON SEQUENCES TO app;

Exit PostgreSQL:
Exit the PostgreSQL prompt:

sql

\q

Update the DATABASE_URL in Your Application:
Ensure your application’s configuration file (e.g., .env) has the correct DATABASE_URL:

dotenv

DATABASE_URL="postgresql://app:your_password@127.0.0.1:5432/app?serverVersion=16&charset=utf8"

Test the Connection:
Run the command to create the database (if not already created) or test your application:

bash

    php bin/console doctrine:database:create

## commandes pour reset la DDB

```

php bin/console doctrine:database:drop --force &&

php bin/console doctrine:database:create &&

php bin/console doctrine:migrations:migrate
```

## commande pour se connecter en cli a la base de dev

```
sudo psql -U app -h localhost -d app
```
