# LearningShare

## commandes

commande qui a été utilisé pour créer le projet (ne pas lancer)
symfony new LearningShare --version="6.4.\*" --webapp

lancer le serv de dev :

```
symfony serve
```

permet de complétement créer/reset la bdd avec les inserts d'origine :

```
php bin/console doctrine:database:drop --force &&
php bin/console doctrine:database:create &&
php bin/console doctrine:migrations:migrate --no-interaction
```

lancer le rechargement automatique de la page apres des modifications :

```
browser-sync start --proxy "http://localhost:8000" --files "**/*.php, **/*.css, **/*.js, **/*.html, **/*.twig" --no-inject-changes
```

permet de créer un component (fichier de class et fichier twig) dans les dossier correpondants :
créer des component pour tout les élements n'est pas utile
il ne faut créer des component que lorsque il vont encapsuler de la logique php

```
php bin/console make:twig-component Alert
```

afficher toutes les routes de l'appli :

```
php bin/console debug:router
```

faire les cruds automatiquement (tous déja fait sauf pour user qui devra ê ajouté après que sam termine l'authentification) :

```
php bin/console make:admin:crud
```

permet de lister tout les components de l'appli :

```
php bin/console debug:twig-component
```

permet de créer une migration vide :

```
php bin/console doctrine:migrations:generate
```
