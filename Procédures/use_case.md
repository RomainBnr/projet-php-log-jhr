# Diagramme de cas d’utilisation – Dashboard de logs

```mermaid
flowchart LR
  A[Lecteur]:::actor
  B[Admin]:::actor
  C[Application qui génère des logs]:::actor

  subgraph S[Dashboard de logs]
    UC1((Consulter les logs))
    UC2((Filtrer les logs))
    UC3((Afficher détail d’un log))
    UC4((Exporter les résultats))
    UC5((Créer règle d’alerte))
    UC6((Recevoir une alerte))
    UC7((Ingestion de logs))
  end

  %% Associations
  A --- UC1
  A --- UC2
  A --- UC3
  A --- UC4
  A --- UC6

  B --- UC1
  B --- UC2
  B --- UC3
  B --- UC4
  B --- UC5
  B --- UC6

  C --- UC7

  %% includes
  UC1 --> UC2
  UC1 --> UC3
  UC1 --> UC4

  classDef actor fill:#fff,stroke:#333,stroke-width:1px
