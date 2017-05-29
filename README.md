# Engagement des groupes locaux et collecte de dons #MAVOIX v2.10.0

Formulaire d'engagement des groupe locaux [#MAVOIX](https://mavoix.info) à faire campagne, et gestion des dons.

Le formulaire se trouve à l'adresse : https://collectifs-locaux.mavoix.info.

La page de collecte de dons se trouve ici : https://collecte.mavoix.info.

Version de test : https://collectifs-locaux.maudry.fr


## Notes de version

### 2.10.0

- Ajout de la référence de don dans le sujet de l'email de confirmation pour faciliter la gestion des réponses

### 2.9.0

- Correction d'un bug dans l'ordre des circonscriptions (arrondissement des valeurs décimales)
- Affichage de l'adresse email des donateurs dans le suivi des promesses de dons

### 2.8.0

- Un article de loi "dispose", il ne "stipule" pas
- Dans le formulaire de promesse de don, le footer prend toute la largeur de l'écran

### 2.7.0

- Ajout d'un champ "Pays" dans l'adresse de donateurs
- Ajout d'un message avant le bouton d'envoi de la promesse de don, informant le donateur qu'il aura accès aux informations bancaires une fois le formulaire validé
- Possibilité d'avoir des lettres dans le n° IBAN

### 2.6.0

- Possibilité pour les mandataires de personnaliser l'ordre à rédiger sur les chèques de don
- Amélioration de l'affichage sur les petits écrans

### 2.5.0

- Possibilité pour les mandataires financiers de réinitiliser leur mot de passe
- Ajout du montant de la promesse de don dans le mail de confirmation

### 2.4.0

- Correction de l'affichage des dates sur les pages de suivis des promesses et des transactions (accès mandataire)
- Réorganisation de la page du collectif local
- Lien direct de la page du collectif local vers le formulaire de promesse

### 2.3.0

- Affichage de toutes les circonscriptions
- Changement du prix unitaire des affiches

### 2.2.0

- Ajout des métadonnées sociales (FB, Twitter)
- Lien vers le budget de chaque circo via la page d'accueil des dons
- Correction de l'inversion frais bancaires / frais comptables pour les circos FDE

### 2.1.0

- Mise à jour du format de l'ordre pour les chèques
- Arrondissement du montant des promesses de dons à deux décimales après la virgule
- Coquilles

### 2.0.0

- Système de promesses de don
- Gestion des transactions par le mandataire financier

### 1.2.0

- Ajout d'une page par collectif local, avec une carte de la circonscription et des informations de contact
- Groupe local > Collectif local

### 1.1.2

- Nombre de collectifs en campagne fixé à 42
- Début de la transition groupe > collectif
- Département des circonscription des Français de l'étrangeer : ZZ > 99

### 1.1.1

- lors de l'édition d'un groupe local, le numéro de téléphone du membre est affiché, non celui du mandataire

### 1.1.0

- interface de validation
- liste publique des circonscriptions

### 1.0.0

- formulaire en place
- pas encore de validation des groupes locaux

## Prerequisites

- PHP 5 or later
- Composer
- sendmail
- PHP mcrypt module
- PHP curl module
- PHP GD module
- MySQL database

## Installation

- Clonage du dépot
```
git clone https://github.com/MaVoix/groupes-locaux.git
```
- Mettre à jour les paquets composer
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
