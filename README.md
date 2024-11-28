# 24SIK001_BACKEND_SYMFONY


## Symfony 7.1 + PHP 8.2 with Docker

**ONLY for DEV, not for production**

### Run Locally

Clone the project

```bash
 git clone git@github.com:gaoubak/24EMI001.git
```

Run the docker-compose

```bash
  docker compose build --no-cache --pull
```
```bash
 docker-compose up -d
```

If you need a database, create a file .env.local and add a line like this example in /src:

```yaml
DATABASE_URL="mysql://root:root@eminence_backend-mysql:3306/24EMI001?serverVersion=8.4.2&charset=utf8mb4&timeout=60"
```


## Docker Compose Configuration
This docker-compose.yml file includes the following services:

- app: PHP application container
- database: MySQL 5.7 database
- phpmyadmin: PhpMyAdmin interface for database management

## Requirements

Out of the box, this docker-compose is designed for a Linux operating system, provide adaptations for a Mac or Windows environment.

- Linux (Ubuntu 20.04 or other)
- Docker
- Docker-compose

## Makefile Commands
Build, start, and stop Docker containers
- Build the environment:```make build```
- Start the containers: ```make start```
- Stop the containers: ```make stop```
- Restart the environment: ```make restart```
- Bring down containers and networks: ```make down```

### Update database schema
```bash
  make update
```

### View logs
```bash
  make logs
```

### Clear the Symfony cache
```bash
  make clear
```
