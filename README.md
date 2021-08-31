# KCMS Symfony Bundle

Version : 1.0

Date : 16/06/2021

Requirements : 
 - PHP 7.2.5
 - Symfony 4.4 or more
 - Symfony Encore bundle
 - Doctrine + Mysql/MariaDB database
 
See composer.json for all the dependencies

---

# 1) What is KCMS Bundle ?

KCMS Bundle is a symfony (4 and 5) bundle providing CMS features to any 
new or already existing symfony applications

The features offered are simple but powerful and meet the most common needs expected of a content manager.
It also offers good scalability due to its open and modular architecture

KCMS features :

- Upload and manage your media by drag'n'drop in the media manager 
- Create multilingual contents, in pur text or in complex html
- Manage easily complex html contents without risk of destroying css styles with the help of "composed content"
- Create multiple page and manage its by drag'n'drop  by adding your content into its differents zone
- Schedule contents by date
- Manage multiple sites (i.e = one site = one domain)
- Easily find and edit your contents directly from the web page
- Adding new features or type of content to KCMS due to its modular architecture


However, due to its quick and easy approach, it may not meet all needs.

KCMS constraints :

- KCMS is a content manager system, not a container manager system : 
You can not manage the entire html of a page directly into the administration. 
It's the prerogative of the front end developer to develop your html (in twig format) and css.
- You cannot construct an entire website application without any code : KCMS is a bundle and not a
full monolithic cms application like drupal or Wordpress. You will certainly need to develop some features by yourself, like forms.


# 2) KCMS installation

First, you need to prepare some configuration files before install the bundle via composer :

- Create configuration files

> Copy the two config files from kcms bundle to your app/config :

```
From :
kcmsbundle/src/Resources/config/kcms_cache.yaml
kcmsbundle/src/Resources/config/kcms.yaml
```

```
To : 
@App/src/config/packages/
```

- Edit route configuration :

> Create a kcms.yaml file in @app/config/routes/ with the following content :

````
kcms:
  resource: '@KcmsBundle/Resources/config/routes/annotations.yaml'
````

- Prepare configuration for multilingual

>Edit @app/config/routes/annotations.yaml with the following :

````
controllers:
    resource: ../../src/Controller/
    type: annotation
    # Kcms Multilingue feature
    # Comment lines below if you do not need translation
    # Uncomment lines in order to force local variable to be present on url
    # Do not forget to set "multilingual.enable" to true on kcms.yaml file
    prefix: /{_locale}
    requirements:
      _locale: 'en|fr_FR|en_UK'
    defaults:
      _locale: 'fr_FR'
````

- Install bundle via composer in php

>composer require karkov/kcms-bundle

- Run KCMS doctrine migration

> bin/console doctrine:migration:migrate

At this time, Kcms feature is ready, 
but the administration is not yet ready.

---
# 3) Installation of KCMS administration

KCMS administration need some css and js files in order to work. KCMS use webpack encore in order to manager theses asset files.


- Edit webpack_encore.yaml

````
    builds:
      kcms: '%kernel.project_dir%/public/bundles/kcms'
      kcms_admin: '%kernel.project_dir%/public/bundles/kcms'
````

Nb : if you already use webpack into your pack, you should add the builds into the conf.
For example :

````
    builds:
      app: '%kernel.project_dir%/public/build'
      kcms: '%kernel.project_dir%/public/bundles/kcms'
      kcms_admin: '%kernel.project_dir%/public/bundles/kcms'
````

- Copy from bundle to public the entire directories (containing compiled js, css and images) :

````
public/bundles/kcms 
public/fileman 
````

> Go to https://{your.domain}/kcms/admin 

Ps : Depending of the configuration of your web server perhaps you will have to change the rights of theses directory if needed.


# 4) Manage security


**VERY IMPORTANT :** 

Security is entirely manage by YOUR application and it is your responsibility to protect your cms administration. 
KCMS bundle come by default without any security controls.
If you need to create security into your application, please refer to the official symfony documentation : 
https://symfony.com/doc/current/security.html

After a security is correctly created, just adding to your access_control security configuration (security.yaml) 
the following two lines :

````
    access_control:
         - { path: ^/kcms/fileman, roles: ['ROLE_ADMIN_KCMS']}
         - { path: ^/kcms/admin, roles: ['ROLE_ADMIN_KCMS'] }
````       

You can choose of course the role(s) you want, depending on your application and roles you have created.
