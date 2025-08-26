```mermaid
flowchart LR
    subgraph Producteur ["VM Producteur"]
        AppWeb["Application Web (génère des logs)"]
    end

    subgraph Collecteur ["VM Collecteur"]
        Rsyslog["Rsyslog (Réception des logs)"]
        DB[(Base de Données)]
        Dashboard["Application Web Dashboard (Affiche les logs)"]
    end
    
    AppWeb -- Envoi des logs --> Rsyslog
    Rsyslog --> DB
    DB --> Dashboard
