INSTALLATION
=============

1) Add repository into composer.json

"repositories": [
    {
      "type": "vcs",
      "url": "https://gitlab2.karkov.fr/karkov/kcmsbundle.git"
    }
],
  
  
TODO : use composer repo instead of vcs repo (using satis) 
  
  
2) Update composer.json

"require": {
    "karkov/kcms-bundle": "1.*"
}


3) update vendor via composer update


4) Add bundle to symfony

edit /config/bundles.php by adding following line :

Karkov\Kcms\KcmsBundle::class => ['all' => true],


5) copy from bundle to app the bundle yaml config file

from 
/vendor/karkov/kcms-bundle/src/Resources/config/kcms.yml

to
/config/packages/kcms.yaml


6)

Add bundle route into /config/routes.yaml

app_kcms:
    resource: "@KcmsBundle/Controller/"
    type: annotation   


6) Edit env config

DATABASE_URL=mysql://login:password@host:port/database


7) update doctrine schema

php bin/console doctrine:schema:update --em=kcms 
