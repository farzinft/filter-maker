# filter-maker

when you need to make query string filters probably you are 
some of persons that use **if statement** to extend **Laravel QueryBuilder** to make your final query, so its a bad method and hard to maintaining your code in the feature,
by this package you can see magic of implementation of1 queryString filters.

----------------------

## step1 ##
 publish config file : 

```
php artisan vendor:publish
```

now in laravel config folder you can see filter-maker config file, suppose you have a user list and want to make query string filter like :

> /users?first_name=test&last_name=test


you can define your config file like :
```
return [
    'UserFilter' => [
       //inputs
        'first_name',
        'last_name'
    ]
];
```
## step2 ##

now run this command:

    

> php artisan make:filter

now in app directory you can see Filters directory and UserFilter class.

## step3 ##

make a custom service provider with this command:

> php artisan make:provider SampleServiceProvider

and add this provider in your app.php config file. we have to defer loading of this provider because we don't need to load this provider in every request.
**a sample provider** :

> Blockquote


