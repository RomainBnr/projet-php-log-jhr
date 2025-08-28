# projet-php-log-jhr

[![Typing SVG](https://readme-typing-svg.herokuapp.com?color=27538D&lines=Bienvenue+sur+le+repository+Github;)](https://git.io/typing-svg)

# Contexte
Nous disposons d'un application To-Do List hébergé sur une machine virtuel Debian avec un seveur web. Cette infrastructure génère un volume imortant de logs système et applicatifs, rendant leur gestion directement sur la machine complexe et peu pratique. 

# Expression du besoin
Pour optimiser la supervision et faciliter l'exploitation des données, ous proposons la mise en place d'une application de centralisation et de visualisation des logs. Cette solution permettra non seulement de simplifier la gestion des journaux générés par l'infrastructure, mais aussi de répondre aux exigences de sécurité et de conformité définies par l'ANSSI en matière de traçailité et de supervision des systèmes.

# Objectif du projet
Ce projet a pour objectif la collecte des données de logs générées par une application, puis leur stockage et leur affichage sur un dashboard de visualisation. 

# Fonctionnalités principales
* Page de connexion permettant de sécuriser l'accès critique aux données,
* Visualisaton des messages de logs sur un dashboard,
* Export des données en CSV, 
* Filatrage de logs par niveau de criticité (Eleve, moyen, faible), 
* Horodatage des événement.

# Critères de perforances
* Affichages des données temps réels,
* Durcir et maintenir à jour le serveur de collecte, 
* Eviter la saturation des logs en supervisant l'espace disque.

# Technologies utilisé
* PROXMOX
* PHP
* SYSLOG
* DEBIAN
* APACHE

# Liste des livrables
* Synoptique
* UseCase
* Diagram de class
* Analyse du document de l'anssi
* Procédure d'installation

# Maquette 
### Page de connexion
![PageLogin](/media/login.png)
### Page de visualisation des données
![VisualisationDashboard](/media/dashboard.png)

## Auteurs
* **Hugo** - [Hugoxplr](https://github.com/hugoxplr)
* **Romain** - [RomainBnr](https://github.com/RomainBnr)

* **Jorge** - [jorgecastrosilva](https://github.com/jorgecastrosilva)


