# Site de présentation du travail d'Ina Zaoui

## Description

Site de présentation du travail d'Ina Zaoui ainsi que celui de ses invités.
Ina est une artiste en photographie qui souhaite partager son travail via ce site de présentation.
Il permet aux invités de se connecter et de voir les images qu'ils ont prises et téléchargées, ainsi que de les supprimer.
Il permet aussi à l'administrateur (Ina) de gérer les invités, en particulier de les autoriser ou non à publier,
Elle peut accéder à l'ensemble des images de tous les invités, les modifier ou les supprimer.
Elle peut également créer des albums et les affecter à des images.

---

## Pré-requis

Avant de commencer, assurez-vous d'avoir les éléments suivants installés sur votre machine :

- **PHP** (version 8.2 ou supérieure)
- **Composer** (gestionnaire de dépendances pour PHP)
- **MySQL** ou un autre système de gestion de base de données compatible 
  (postgresql est également recommandé mais n'est pas supporté par le pipeline d'intégration continue)
- **Symfony CLI** (recommandé pour les projets Symfony)
- **Symfony 7.1** 
- Serveur web compatible (Apache, Nginx, ou Symfony Server)

---

## Installation

**Clonez le dépôt du projet :**

```bash
git clone https://github.com/Alan971/876-p15-inazaoui.git
```

ou

```bash
git clone git@github.com:Alan971/876-p15-inazaoui.git
```

**Installez les dépendances :**

```bash
cd 876-p15-inazaoui
composer install 
```

(dans le cas d'utilisation de docker suivez la précédure add hoc :  docker exec -it ...)

**Editer le fichier .env.local**

avec vos informations de connexion à la base de données.

    DATABASE_URL=mysql://user:password@127.0.0.1:3306/inazaoui?serverVersion=10.3.39-MariaDB&charset=utf8mb4

**Créez la base de données et exécutez les migrations :**
```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```	

**Lancer Symfony CLI**
```bash	
symfony server:start
```

Ou configurez un serveur local comme Apache ou Nginx.


## Usage

**Accédez à l'application dans votre navigateur :**

URL par défaut : http://127.0.0.1:8000 (si vous utilisez Symfony Server et que vous avez installé en local).


**Commandes utiles :**

Les données de fixtures tels que les fichiers images sont stockées dans le dossier public/uploads/
ajouter des fixtures de données :
```bash	
symfony console doctrine:fixtures:load
```	

**Tester le projet (installez les outils de test au préalable):**
Lancer les tests PHPUnit :

    ./vendor/bin/phpunit

Lancer les test phpstant :

    ./vendor/bin/phpstan 

## Structure du projet

    src/ : Code source PHP
    templates/ : Fichiers Twig pour les vues
    tests/ : Fichiers de tests PHPUnit
    public/ : Dossier public (point d'entrée web)
    migrations/ : Scripts de migration pour la base de données