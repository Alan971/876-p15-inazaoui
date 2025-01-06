# CONTRIBUTING.md

## Introduction

Merci de votre intérêt pour ce projet open-source ! Ce site web, construit avec Symfony, Twig, et CSS, vise à proposer un site de gestion et de visionnage de contenu en direction des artistes et de leur communauté. Votre contribution, qu'elle soit petite ou grande, est précieuse pour l'amélioration de ce projet.

## Prérequis techniques

Pour contribuer à ce projet, vous aurez besoin des compétences et outils suivants :

- **Framework** : Symfony (votre version actuelle ici).
- **Langages** : PHP, HTML/CSS, Twig.
- **Normes de code** : Respecter les standards PSR-2.
- **Outils recommandés** : Composer, Docker (optionnel), PHPUnit pour les tests unitaires. PhpStan.

## Configuration de l'environnement local

Les instructions complètes pour l'installation et la configuration initiale se trouvent dans le fichier `README.md`. Toutefois, voici quelques éléments clés liés à la contribution :

1. **Base de données** : Configurez la base de données selon les indications du `README.md`.
   - Importez les fixtures avec la commande : `symfony console doctrine:fixtures:load`.
2. **Dossier d'images** : Importez le dossier compressé fourni pour les images d'exemple.
3. **Docker (optionnel)** : Vous pouvez utiliser Docker pour simplifier la configuration. Assurez-vous d'avoir les fichiers Docker à jour si vous choisissez cette option.

## Normes et bonnes pratiques

Pour garantir la cohérence et la qualité du projet, veuillez respecter les éléments suivants :

- **Normes de code** : Suivez le standard PSR-2. Assurez-vous que votre code est clair, lisible et bien commenté.
- **Tests** : Lancez tous les tests PHPUnit avant de soumettre votre contribution pour vérifier que votre code n'introduit pas de régressions.
- **Commits Git** : Adoptez des messages de commit concis et significatifs.

## Processus de contribution

1. **Signalement d'issues** : Si vous détectez un problème ou avez une suggestion, ouvrez une issue dans la section correspondante sur GitHub.
2. **Proposer une modification** :
   - Forkez le dépôt et travaillez sur une branche spécifique.
   - Testez votre code pour vous assurer qu'il respecte les normes et les bonnes pratiques.
   - Soumettez une pull request en expliquant clairement votre modification.
3. **Documentation** : Si votre modification est importante, documentez-la dans un fichier `CHANGELOG.md` ou dans la documentation appropriée du projet.

## Tests

Ce projet utilise PHPUnit pour les tests unitaires et PhpStan pour les tests de qualité du code. Avant de soumettre une contribution, assurez-vous que tous les tests passent et ajoutez-en si nécessaire.

Exécutez les tests avec la commande suivante :

```bash
./vendor/bin/phpunit
./vendor/bin/phpstan
```

## Moyens de communication

Si vous avez des questions ou besoin d'aide, utilisez la section issues de GitHub. Les mainteneurs du projet feront de leur mieux pour répondre rapidement à vos demandes.

Merci encore pour votre contribution et bonne collaboration !

 