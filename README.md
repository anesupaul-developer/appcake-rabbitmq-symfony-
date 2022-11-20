# NEWS PARSER - SYMFONY 5.4 RABBITMQ NGINX MYSQL DOCKER PHP 7.4

 ** Written by Anesu Paul Ngirande : dripdatadev@gmail.com **

REPLICATE THE FOLLOWING STEPS TO HAVE THE APP RUN ON YOUR MACHINE

   - Install vendor packages by going into app folder and run the command composer install

## STEP 1: Setup DOCKER
In your terminal run 
	
	- docker compose up -d --build

## STEP 2: RUN YOUR MIGRATIONS
Before running the following commands make sure ac_container_mysql is up and running and test mysql connection and manage to run the command CREATE DATABASE appcake

In your terminal run
	- docker compose exec php bin/console doctrine:migrations:migrate
	
## STEP 3: RUN YOUR USER SEEDER
You can prepopulate your users table with details inside the app/src/Command/SeedUsersCommand

	- docker compose exec php bin/console seed:users

now in your browser visit you http://localhost:8080. (you can check your nginx url inside docker desktop on your nginx-container)

Login with details obtained form the SeedNewsCommand

## STEP 4: PARSE NEWS SERVICE
In this project example I used the https://newsapi.org, you can use a different api. Just make sure the NewsArticle definition is the same.

In your terminal now run the following command

	- docker compose exec php bin/console app:news --date=[DATE_TO_RETRIEVE_NEWS 2022-11-20]
	** eg docker compose exec php bin/console app:news --date=2022-11-18 **

This send the articles to the rabbit mq controlled queue

## STEP 5: CONSUME NEWS ARTICLE MESSAGES VIA THE TERMINAL
	- docker compose exec php bin/console messenger:consume async -vv

