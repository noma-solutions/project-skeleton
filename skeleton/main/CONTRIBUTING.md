[name] - Contributing
=====================

1. [Uruchomienie środowiska dev](#uruchomienie-środowiska-dev)
2. [Deployment](#deployment)
3. [Standard opisywania CHANGELOG](#standard-opisywania-changelog)
4. [Struktura repozytorium GIT i MRy](#struktura-repozytorium-git-i-mry)

## Uruchomienie środowiska dev

### Wymagania

- Docker:
    - [Docker - Instalacja](https://docs.docker.com/install/overview/)
    - [Post install](https://docs.docker.com/install/linux/linux-postinstall/) - szczególnie _Manage Docker as a non-root user_
- Docker Compose:
    - [Docker-Compose - Instalacja](https://docs.docker.com/compose/install/)

### Cheatsheet

- __Uruchomienie kontenerów w tle (rekomendowane)__: `sudo docker-compose up -d`
- Uruchomienie kontenerów z podglądem: `sudo docker-compose up`
- Zatrzymanie kontenerów: `sudo docker-compose stop`
- Zabicie kontenerów: `sudo docker-compose kill`
- __Podgląd logów__: `sudo docker-compose logs`
- Polecenia do obsługi aplikacji:
    - Odpalenie shella w kontenerze php-fpm: `sudo docker-compose exec php-fpm bash`
    - Obsługa composer'a: `sudo docker-compose exec php-fpm composer [polecenie]`
    - Odpalenie `bin/console`: `sudo docker-compose exec php-fpm bin/console`
    - Klient bazy: `sudo docker-compose exec mysql mysql -uns01143 -pns01143`

__UWAGA:__ Czyszczenie cache, wykorzystywanie compose'a etc. odpalane z poziomu kontenera powoduje, że powstałe pliki
mają ownera ustawionego jako `root` - należy pamiętać o zmianie uprawnień (`chown -R user:user ./*`).

### Uruchomienie

1. Uruchamiamy kontenery `docker-compose up -d` i... już.

## Deployment

### Wymagania:

- [Ansible](https://www.ansible.com/)
- [Ansistrano](https://github.com/ansistrano/deploy)
- [Ansistrano Symfony Deploy](https://github.com/cbrunnkvist/ansistrano-symfony-deploy)

### Uruchomienie

__UWAGA__ Przed aktualizacją środowiska __test__ należy zmergować aktualną wersję do brancha test.

1. `cd .deploy`
2. `ansible-playbook -i ./hosts/hosts_[dev|test] ./deploy.yml` (`[dev|test]` oznacza, że należy wybrać jeden z plików)

## Backend

### Development

Przed przystąpieniem do pracy nad nowymi funkcjonalnościami wykonujemy polecenie w konsoli 
dockera (php): `./vendor/bin/robo app:update`

Powyższe polecenie:
- wykonuje `composer install`
- czyści cache
- uruchamia migracje
- ustawia uprawnienia do folderów

W przypadku gdy potrzebne jest wyczyszczenie bazy danych można użyć polecenia: `./vendor/bin/robo app:reset`

### Testy

- Testy inicjalizujemy poleceniem: `vendor/bin/robo test:init`
- PHPUnit
    - @TBD
- Robot Framework
    - Testy RobotFW uruchamiany przechodząc do kontenera testowego poleceniem `docker-compose run robotfw bash`
    - Nastepnie uruchamiamy wszystkie suity poleceniem `./run.sh` lub pojedynczy test: `robot suites/security.robot`
    - W przypadku błędów związanych z brakującymi bibliotekami podczas uruchamiania testów należy na początek wykonać polecenie: `pip install --upgrade --force-reinstall -r requirements.txt`


