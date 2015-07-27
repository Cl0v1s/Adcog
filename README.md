# Adcog

http://adcog.fr

## Configuration

Projet LAMP Symfony2.

## Apache2

```bash
# User : root

apt-get install apache2
a2enmod rewrite

cat > /etc/apache2/sites-available/adcog.conf <<EOF
<VirtualHost *:80>
    ServerName adcog.localhost
    DocumentRoot /var/www/Adcog/web
    <Directory /var/www/Adcog/web>
        AllowOverride All
        Require all granted
        Allow from All
    </Directory>
    ErrorLog /var/log/apache2/adcog-error.log
    CustomLog /var/log/apache2/adcog-access.log combined
</VirtualHost>
EOF

a2ensite adcog

service apache2 reload
```

### PHP5

```
# User : root

apt-get install php5 php5-gd php5-curl
```

### MySQL

```
# User : root

apt-get install mysql-server
```

### GIT

```
# User : root

apt-get install git
```

### Sources

```bash
# User : root

mkdir /var/www/Adcog
chown -R www-data. /var/www/Adcog

echo '127.0.0.1 adcog.localhost' >> /etc/hosts

cd /var/www
curl -sS https://getcomposer.org/installer | php
chown www-data. composer.phar
chmod +x composer.phar

su - www-data <<<EOF

cd /var/www/Adcog
git clone git@github.com:emmanuelballery/Adcog.git .

cat > env.php <<EOL
<?php
define('ENV', 'dev');
define('KEY', `date +%s`);
\$_SERVER['SYMFONY__VERSION'] = KEY;
setlocale(LC_ALL, 'fr_FR.utf8');
mb_internal_encoding('utf-8');
date_default_timezone_set('Europe/Paris');
setlocale(LC_ALL, 'fr_FR.utf8', 'fr_FR');
EOL

/var/www/composer.phar install

php app/console doctrine:database:create
php app/console doctrine:schema:update
php app/console doctrine:fixtures:load
php app/console assets:install --symlink
php app/console assetic:dump

EOF

service apache2 restart

```

## Architecture

6 bundles pour 6 rôles :

  * DefaultBundle : socle + front non sécurisé
  * UserBundle : accès aux utilisateurs (ROLE_USER)
  * MemberBundle : accès aux membres (ROLE_MEMBER)
  * ValidatorBundle : accès aux validateurs (ROLE_VALIDATOR)
  * BloggerBundle : accès aux bloggers (ROLE_BLOGGER)
  * AdminBundle : accès aux administrateurs (ROLE_ADMIN)

Rôles :

  * Anonymous
  * ROLE_USER : utilisateur enregistré mais pas forcément membre de l'association
  * ROLE_MEMBER : membre de l'association (calcul effectué en fonction des dates, promotions ...)
  * ROLE_VALIDATOR : peut valider les comptes et les paiements
  * ROLE_BLOGGER : peut blogger et configurer les textes
  * ROLE_ADMIN : peut tout administrer

Hiérarchie des rôles :

    * ROLE_BLOGGER est automatiquement ROLE_VALIDATOR
    * ROLE_ADMIN est automatiquement ROLE_VALIDATOR et ROLE_BLOGGER
    * Les rôles > ROLE_MEMBER ne sont pas attribués lorsque l'utilisateur n'est plus membre