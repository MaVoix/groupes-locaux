# Engagement des groupe locaux #MAVOIX v1.0.0

Formulaire d'engagement des groupe locaux [#MAVOIX](https://mavoix.info) à faire campagne, et gestion des dons.

Le formulaire se trouve à l'adresse : https://groupes-locaux.mavoix.info.

Version de test : https://groupes-locaux.maudry.fr


## Notes de version

### 1.0.0

- formulaire en place
- pas encore de validation des groupes locaux

## Prerequisites

- PHP 5 or later
- Composer
- PHP mcrypt module
- PHP curl module
- PHP GD module
- MySQL database

## Installation

- Clonage du dépot
```
git clone https://github.com/MaVoix/groupes-locaux.git
```
-  Mettre à jour les paquets composer
```
  php composer.phar update
```
- Paramétrez votre VHOST sur /web/
- Créer/modifier le fichier **config.php** à partir du config.sample.php
- Créer/modifier le fichier **maintenance.php** à partir du maintenance.sample.php
- Ajouter son adresse IP dans maintenance.php (important pour la compilation JS et CSS !)
- Importer la structure de la base **/sql/CREATE-DATABASE.sql**
- Créer un utilisateur admin (utiliser la fonction PASSWORD  sur le champ "pass" )
- Mettre les **droits en écriture** sur les dossiers **/web/js**,**/cache**,**/tmp** et **/web/css**
- Compiler les fichiers JS et CSS en appelant les liens **/tool/make-js.php** et **/tool/make-css.php**
