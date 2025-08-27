# Procédure complète : Installation et configuration de rsyslog

## 1. Contexte
- **Serveur A (expéditeur / application)** : `PHP-APP` — IP `192.168.1.168`
- **Serveur B (récepteur / centralisation)** : `PHP-BDD` — IP `192.168.1.128`

Objectif : tous les logs générés sur **PHP-APP** sont envoyés et stockés sur **PHP-BDD** dans `/var/log/remote/PHP-APP/`.

---

## 2. Installation de rsyslog

### Sur les deux serveurs (A et B)
```bash
sudo apt update
sudo apt install -y rsyslog
sudo systemctl enable rsyslog --now
sudo systemctl status rsyslog
```

---

## 3. Configuration du serveur B (PHP-BDD)

### 3.1 Fichier de configuration

Créer le fichier `/etc/rsyslog.d/10-receiver.conf` :

```bash
module(load="imudp")
input(type="imudp" port="514")

module(load="imtcp")
input(type="imtcp" port="514")

template(name="RemoteSyslog" type="string"
         string="/var/log/remote/%HOSTNAME%/syslog.log")

action(type="omfile" DynaFile="RemoteSyslog" createDirs="on")
```

### 3.2 Création du répertoire
```bash
sudo mkdir -p /var/log/remote
```

### 3.3 Redémarrage du service
```bash
sudo systemctl restart rsyslog
```

### 3.4 Ouverture des ports
```bash
sudo ufw allow 514/tcp
sudo ufw allow 514/udp
```

### 3.5 Vérification
```bash
sudo ss -lunpt | grep 514
```

---

## 4. Configuration du serveur A (PHP-APP)

### 4.1 Fichier de configuration

Créer le fichier `/etc/rsyslog.d/10-forward.conf` :

```bash
*.* @@192.168.1.128:514
```

### 4.2 Redémarrage du service
```bash
sudo systemctl restart rsyslog
```

---

## 5. Test de fonctionnement

### 5.1 Génération d'un log sur le serveur A
```bash
logger -t testapp "Test log depuis PHP-APP vers PHP-BDD"
```

### 5.2 Vérification sur le serveur B
```bash
ls -R /var/log/remote
tail -n 50 /var/log/remote/PHP-APP/syslog.log
```

### 5.3 Exemple attendu
```
2025-08-26T10:24:27+02:00 PHP-APP testapp: Test log depuis PHP-APP vers PHP-BDD
```