# Procédure d’installation d’une VM Debian 12 sur Proxmox

## 1. Préparation de l’ISO Debian 12

1. Télécharger l’image ISO officielle de Debian 12

2. Depuis l’interface **Proxmox VE** :
   - Aller dans **Datacenter → Stockage (local)**  
   - Onglet **ISO Images**  
   - Cliquer sur **Upload**  
   - Importer l’ISO téléchargée  

---

## 2. Création de la VM Debian 12

1. Cliquer sur **Create VM** (en haut à droite).  
2. Étapes principales :
   - **General** :  
     - Node : choisir le nœud  
     - VM ID : automatique ou personnalisé  
     - Name : ex. `PHP-APP`  

   - **OS** :  
     - Choisir l’ISO : `debian-12.9.0-amd64-netinst.iso`   

   - **Disks** :  
     - Bus/Device : `VirtIO Block` ou `SCSI`  
     - Storage : `local-lvm` ou autre stockage choisi  
     - Disk size : ex. `50 GiB` (selon besoins)   

   - **CPU** :  
     - Sockets : `1`  
     - Cores : ex. `1`  
     - Type : `host`  

   - **Memory** :  
     - RAM : ex. `2048 MB` 

   - **Network** :  
     - Bridge : `vmbr0`  
     - Model : `VirtIO (paravirtualized)`  

3. Cliquer sur **Finish** pour créer la VM.  

---

## 3. Installation de Debian 12

1. Démarrer la VM.  
2. Ouvrir la console.  
3. Lancer l’**installation standard Debian** :  
   - Choisir la langue, le fuseau horaire, le clavier  
   - Configurer l’utilisateur root et le compte utilisateur  
   - Partitionner le disque
   - Sélectionner les logiciels (SSH server, Web server.)  
   - Installer GRUB sur le disque principal  

4. Redémarrer la VM à la fin de l’installation.  

