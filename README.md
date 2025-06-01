# LearningShare

## commandes

commande qui a été utilisé pour créer le projet (ne pas lancer)
symfony new LearningShare --version="6.4.\*" --webapp

après chaque git pull ne pas oublier de tenter un :

```
composer install
```

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

permet de générer les fichiers de traduction :

```
php bin/console translation:extract --force en
```

permet de créer un component (fichier de class et fichier twig) dans les dossier correpondants :
créer des component pour tout les élements n'est pas utile
il ne faut créer des component que lorsque il vont encapsuler de la logique php

```
php bin/console make:twig-component nowComposant
```

générer un composant de type live :

```
php bin/console make:twig-component --live EditPost
```

afficher toutes les routes de l'appli :

```
php bin/console debug:router
```

faire les cruds automatiquement (pour l'instant ils ne ne marchent pas)

```
php bin/console make:admin:crud
```

permet de créer/modifier une entité

```
php bin/console make:entity
```

permet de lister tout les components de l'appli :

```
php bin/console debug:twig-component
```

permet de créer une migration vide :

```
php bin/console doctrine:migrations:generate
```
Se connecter en tant qu'administrateur pour accéder au tableau de bord d'administration de l'application :

```
mail : admin@learningshare.com
mot de passe : admin123
```

pour faire fonctioner les stimulus live function on peut tweak les déclencheur comme ca :

data-action="click->live#action"

### DEPLOYER EN PROD (https://learningshare.alwaysdata.net/)

Créer la commande en local
```
git remote add deploy learningshare@ssh-learningshare.alwaysdata.net:~/learningshare.git
```
Pousser la main
```
git push deploy main
mot de passe : 1VXaQ33kF5
```
