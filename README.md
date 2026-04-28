# Prestalis Ticketing

Système de support client autonome intégré à WordPress pour la gestion des tickets de service.

## Description

Prestalis Ticketing permet une interaction fluide entre les clients et l'équipe technique via un système de tickets basé sur des jetons d'accès uniques. Aucun compte utilisateur n'est nécessaire pour le client, assurant une simplicité d'utilisation maximale.

## Fonctionnalités

- **Création de tickets :** Formulaire côté admin pour initialiser les échanges.
- **Accès sécurisé :** Suivi via jeton (token) unique, sans authentification lourde.
- **Historique des échanges :** Interface de discussion en temps réel entre client et admin.
- **Gestion des statuts :** Suivi de l'état des tickets (Ouvert, En attente, Résolu).
- **Notifications :** Alertes automatiques par email lors de chaque nouvelle réponse.

## Installation

1. Télécharger ou cloner le dossier dans `/wp-content/plugins/`.
2. Activer le plugin depuis le tableau de bord WordPress.
3. Le menu "Support Prestalis" apparaîtra dans votre barre latérale admin.

## Structure du Plugin

- `prestalis-ticketing.php` : Cœur du plugin et logique métier.
- `assets/` : Fichiers de style (CSS) pour l'interface.
- `includes/` : Templates pour le dashboard admin et le suivi client.

## Technologies utilisées

- PHP 8.x
- WordPress API (wpdb, wp_mail, hooks)
- CSS3

## Auteur

Éloïse Robert - La Fabrique du Numérique du 41.
