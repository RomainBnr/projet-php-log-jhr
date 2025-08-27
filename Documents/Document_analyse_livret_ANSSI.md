## 1. Conditions applicables les plus importantes (priorité)


R1 : Utiliser des solutions avec journalisation native

R2 : Activer la journalisation sur un maximum d’équipements (serveurs, VM, services critiques)

R3 + R4 + R5 : Horodater les événements + homogénéiser les horodatages + synchroniser les horloges (NTP)

R9 : Centraliser les journaux (rsyslog vers la VM de collecte)

R14 : Privilégier un transfert en temps réel (rsyslog → syslog central)

R19 : Durcir et maintenir à jour le serveur de collecte

R22 : Superviser l’espace disque (éviter saturation des logs)

Socle minimal (Annexe A) : Authentification, gestion des comptes, accès aux ressources sensibles, modifications de sécurité


## 2. Conditions intéressantes mais difficiles à mettre en place sur la semaine



R7 : Journaliser les empreintes de fichiers suspects

R15 : Faire une analyse de risques sur le mode de transfert (push/pull)

R16 & R17 : Utiliser des protocoles fiables et sécurisés pour transfert (TLS syslog, pas trivial à monter vite)

R20 : Cloisonner les serveurs de collecte dans une zone dédiée (demande d’archi réseau plus lourde)

R23+ : Stocker les logs dans une base indexée (ex: ELK/Graylog → trop complexe dans 4 jours)

R25 : Durées de rétention conformes à la réglementation (besoin analyse légale + CNIL)


## 3. Conditions que nous ne souhaitons pas mettre en oeuvre



R28–R30 : Externalisation + recours à un PDIS (trop complexe et hors sujet ici)

R31 : Collecte des journaux des postes nomades via VPN (pas concerné si uniquement VM fixes)

R26 & R27 : Droits d’accès très granulaires sur lecture/écriture/suppression des logs (important en prod, mais pas vital en démo)