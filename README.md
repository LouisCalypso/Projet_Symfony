# Français :

## Pour récupérer le projet en local : 
Après un git clone en local sur votre PC (il faut récupérer le lien, exemple : git clone https://github.com/LouisCalypso/Projet_Symfony.git) 
Puis go sur la branche dev puis pull le code de github :
```
git checkout dev
git pull
```

Vous aurez ensuite besoin de faire composer dump-autoload puis composer install pour mettre à jour et installer les dépendances
Si vous avez bien récupéré le fichier .env avec le nom symfodoggos il faudra créer la BDD :

```
php bin/console doctrine:database:create
```

Retirer les versions de migrations dans le dossier Migrations s’il y a puis :

```
php bin/console doctrine:migration:diff 
php bin/console doctrine:migration:migrate
```

Puis on charge les fausses données si on veut :
```
php bin/console doctrine:fixtures:load
```
Lancer le projet :
```
php -S localhost:8080 -t public
```

# English :

## To recover the project locally:
After a git clone locally on your PC (you must recover the link, example: git clone https://github.com/LouisCalypso/Projet_Symfony.git)
Then go to the dev branch then pull the github code:
```
git checkout dev
git sweater
```
You will then need to do composer dump-autoload then composer install to update and install the dependencies
If you have recovered the .env file with the name symfodoggos, you will have to create the database:

```
php bin/console doctrine:database:create
```
Remove the migration versions in the Migrations folder if there is then:
```
php bin/console doctrine:migration:diff 
php bin/console doctrine:migration:migrate
```
Then we load the false data if we want:
```
php bin/console doctrine:fixtures:load
```
Launch the project:
``` 
php -S localhost:8080 -t public
```


