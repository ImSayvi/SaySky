FROM php:8.4-apache

# Pakiety systemowe potrzebne do rozszerzeń PHP i Composera; na końcu sprzątamy listę pakietów
RUN apt-get update && apt-get install -y --no-install-recommends \
        libzip-dev \
        libpng-dev \
        libicu-dev \
        git zip unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql zip gd intl

#wrzucam composera do kontenera; linijka mówi skąd z obrazu (/composer) gdzie w moim kontenerze ma się znaleźć (/usr/bin/composer) - tu domyślnie linux szuka programów
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

#tu ustawiamy katalog w którym będzie nasza aplikacja, czyli katalog public, bo tam jest index.php i tam będą wszystkie pliki które mają być dostępne z poziomu przeglądarki
ENV APACHE_DOCUMENT_ROOT=/var/www/public

RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

#generalnie jeśli leci zapytanie o sport/43 i takiego pliku nie ma ,to trzeva vędzie go przekierować na index.php i tam go obsłużyć, dlatego włączamy mod_rewrite
RUN a2enmod rewrite

#to jest workdir kontenera, czyli katalog w którym będziemy pracować, czyli w tym katalogu będą nasze pliki
WORKDIR /var/www

#to mi kopiuje mój kod z katalogu w którym jest Dockerfile do katalogu /var/www w kontenerze, czyli do workdir
#innymi słowy - to przenosi mój kod do kontenera
COPY . /var/www

# copy zapisuje jako root. jeśli aplikacja ma działać jako www-data, to trzeba zmienić właściciela plików na www-data
COPY --chown=www-data:www-data . .

EXPOSE 80