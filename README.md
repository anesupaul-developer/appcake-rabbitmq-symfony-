# NEWS PARSER - SYMFONY 5.4 RABBITMQ NGINX MYSQL DOCKER PHP 7.4

Written by Anesu Paul Ngirande


# REPLICATE THE FOLLOWING STEPS TO HAVE THE APP RUN ON YOUR MACHINE

##STEP 1: Setup DOCKER

In your terminal run docker compose up -d --build

## STEP 2: RUN YOUR MIGRATIONS AND SEEDERS
Before running the following commands make sure ac_container_mysql is up and running and test mysql connection and manage to run the command CREATE DATASE appcake

In your terminal run 
	- docker compose exec php bin/console doctrine:migrations:migrate
	- docker compose exec php bin/console seed:users
	- Optionally to start seeing data you can run (docker compose exec php bin/console seed:news)
	
- now in your browser visit you http;//localhost:8080. (you can check your nginx point inside docker desktop on your nginx-container)