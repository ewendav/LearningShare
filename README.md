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

faire les cruds automatiquement (tous déja fait sauf pour user qui devra ê ajouté après que sam termine l'authentification) :

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

pour faire fonctioner les stimulus live function on peut tweak les déclencheur comme ca :

data-action="click->live#action"

j'ai ajouté des inserts de tests dans les migration

j'ai rédiger le readme avec des infos lié au devellopment pour nous

j'ai ajouté l'écran d'afficahge des sessions

j'ai ajouté le fait de dynamiquement récupréer les sessions fonction du slider grace au compo live symfony

j'ai ajouté le mode light et le mode dark

j'ai implémnter le sys de traduction

j'ai commencé a ajouté le fait de pouvoir faire des recherches avec des filtres précis (et c'eest dynamique) mais j'ai pas fini vu que j'utiliserais surement les méhtodes de gab de son crud donc ca sert a r que j'aille plus loin (c'est une demande explicite des cosnignes)
j'ai pas fais le style de ca je verrais plus tard

dcp la ce qu'il nous reste a faire pour les deux semaines :

sam faut que tu ajoute l'authentificaiton et tu peut aussi faire les écrans de création de compte parceque j'ai la flm de tous me les taper, faudra surement utiliser les créateurs de formulaires qu'on avu en cm et les méthodes de models (ca s'appel repository sur symfony) pour rejoindre ou créer son compte

gab la deuxieme semaine, faire l'ajout des crud avec easyadmin, j'ai tenter de les faire mais c'est pas aussi plug and play que le prof le disait, ou alors j'ai mal fait qlqchose (ta pas besoin de l'auth pour faire les crud c'est juste mieux pour tester)
