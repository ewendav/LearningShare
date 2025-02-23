# LearningShare

## peut ê nécessaire : installer l'utilitaire en ligne de commande symfony

les instuctions d'instalation sont ici <https://symfony.com/download>

## instuctions pour le premier lancement du serv de dev avec docker

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
