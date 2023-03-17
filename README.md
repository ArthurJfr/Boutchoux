# Framework Pédagogique MVC6

## Prérequis
- PHP >= 7.4.*
- Composer
- Node - npm
- MySQL avec PDO

## Installation

```bash
  git clone https://gitlab.com/quidelantoine/mvc6.git
  cd mvc6
  composer dump-autoload 
  npm install
```

## Configurations

Par sa simplicité, MVC5 requière peu de configuration pour fonctionner.

### Le fichier de configuration

MVC5 est livré avec un fichier nommé config/config-dist.php.
Ce fichier est destiné à être versionné, et ne doit pas contenir d'informations personnelles ou sensibles.
Le fichier lu par défaut par le framework est <span class="code">config/config.php</span>, qui lui, ne doit pas être versionné, il vous est personnel.

Pour démarrer copier-coller le contenu du fichier config/config-dist.php dans
```php
/* config/config.php */
return array(
    'db_name'   => 'dbname',
    'db_user'   => 'root',
    'db_pass'   => '',
    'db_host'   => 'localhost',
    
    'version' => '1.0.0'
);
```
## Serveur php & Webpack
```bash
// Pour lancer serveur PHP
php -S localhost:2323 -t public
// Pour lancer Webpack
npm run watch
// Pour build Webpack
npm run build
```
## Authors
- [@quidelantoine](https://www.github.com/quidelantoine)
