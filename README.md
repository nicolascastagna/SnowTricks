# Projet 6 - Développez de A à Z le site communautaire SnowTricks
### Parcours Développeur d'application PHP/Symfony

## Prérequis

- Serveur local sous PHP 8.2 ([MAMP](https://www.wampserver.com/) pour macOs ou [WAMP](https://www.mamp.info/en/mamp/mac/) pour windows)
- [Symfony](https://symfony.com/download)
- Base de donnée MySQL
- [Composer](https://getcomposer.org/)
  
## Installation du projet

**1 - Cloner le dépôt GitHub :**
```
git clone https://github.com/nicolascastagna/SnowTricks.git
```

**2 - Installer les dépendances :**
```
composer install
```

**3 - Copier le fichier **.env.example** et renommer le en **.env** et modifier les paramètres de connexion à la base de données / gmail :**
```
# DATABASE_URL="mysql://app:!ChangeMe!@127.0.0.1:3306/app?serverVersion=8&charset=utf8mb4"
###> symfony/mailer ###
MAILER_DSN=smtp://adresse_email@gmail.com:password_application@smtp.gmail.com:587?encryption=tls&auth_mode=login&username=adresse_email
SENDER_EMAIL="example@example.com"
###< symfony/mailer ###
```

**4 - Créer la base de données :**   
    
    A. Effectuer les commandes suivantes :
        - php bin/console doctrine:database:create
        - php bin/console doctrine:migrations:migrate
    B. Insertion des données de démonstration (optionnel) :
        - php bin/console doctrine:fixtures:load
      

**5 - Démarrer le serveur web :**   

Démarrez le serveur web en exécutant la commande suivante :
```
symfony server:start
```

**6 - Informations de connexions utilisateurs par défaut SI éxécution des fixtures**

**Rôle Admin**
- **Email de connexion :** admin@admin.com
- **Mot de passe :** admin1234

**Rôle Utilisateur**
- **Email de connexion :** user@user.com
- **Mot de passe :** user1234

Si vous n'avez pas éxécuté la commande des fixtures, il vous faudra créer un utilisateur en suivant ces étapes :    
- Créé un utilisateur depuis le formulaire d'inscription    
- Activez le compte en cliquant sur le lien envoyé par mail    
- Pour avoir un rôle admin, il suffit de modifier manuellement le rôle de l'utilisateur dans la table user    

**7 - Paramétrage de Gmail**

Il est nécessaire de configurer votre gmail pour pouvoir tester l'envois de mail. Accédez aux paramètres de sécurité de votre compte Google, recherchez l'option **Mots de passe d'application** et générez un [mot de passe d'application](https://myaccount.google.com/apppasswords) pour l'ajouter au fichier .env.
