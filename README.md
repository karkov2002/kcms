1) Add repository on composer.json

"repositories": [
    {
        "type": "vcs",
        "url": "https://github.com/karkov2002/kcms.git"
    }
],


2) install via composer

composer require karkov/kcms-bundle

3) Move config file
   Resources/config/kcms.yaml > App/config/packages

