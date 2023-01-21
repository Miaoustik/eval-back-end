Ref étudiant: GDDWWMECFENTRIII2A_296117_20221120181557
Adresse en ligne: http://www.studimiaoustik.live/


Déploiement en local:

    Prérequis:
        -php 8.1 avec les extensions (ainsi que l'extension pdo correspondant à votre SGBDR.)
        -un SGBDR
        -composer
        
    Instructions: 
        -cloner le projet de github
        -créer un fichier .env et définir les variables APP_SECRET, APP_ENV,  DATABASE_URL, MAILER_DSN et APP_EMAIL.
        (APP_EMAIL sera l'adresse email émettrice des emails de candidatures)
        -se rendre à la racine du projet et faire un composer install.
        -faire php bin/console doctrine:database create si vous n'avez pas encore créer votre base de donnée.
        -faire un php bin/console doctrine:migrations:migrate pour préparer votre base de données à recevoir les données.
        -tester le serveur a faisant php bin/console server:start

    Un admin peut être créé avec :
        -php bin/console app:add-admin
        -une adresse mail et un mot de passe en clair vous sera demandé.

Déploiement en ligne (heroku):

	-prérequis : 
        -un compte heroku avec dyno eco plan minimum et un addon postgres pour la base   de données.
		-avoir git.
		-installer heroku sur votre machine

	-installation: 
		-cloner le projet.
		-sur rendre dans le dossier de votre projet.
		-faire un heroku login pour vous connecter à votre compte. 
		-heroku create pour créer votre appli dans heroku.
        -déclarer toute les variables d'environnement dans heroku config:set ou dans la page settings de votre app sur heroku.
		-faire git push heroku main pour build votre app locale dans heroku.
		-heroku ps:scale web=1 pour démarrer une instance de votre application.
		-heroku open pour tester votre app dans le navigateur.
        -prendre un nom de domaine personnaliser et le lier a votre app heroku. (L’envoi de mail nécessite un nom de domaine personnalisé) 
		- ajouter les records DNS pour lier votre nom de domaine a heroku.

Pour l’envoi de mail prendre un service comme mailgun, le lier à votre nom de domaine. La variable APP_EMAIL correspond à l'email utilisée pour l’envoi de mail. Tandis que MAILER_DSN correspond a votre identifiant SMTP.

Pour la création d'admin, la commande php bin/console app:add-admin a été ajouté à l'application. Un email et un mot de passe en clair qui sera encrypté par la commande vous seront demandés par la suite. 
		
		
	
	
