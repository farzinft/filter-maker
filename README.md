# filter-maker

when you need to make query string filters probably you are 
some of persons that use **if statement** to extend **Laravel QueryBuilder** to make your final query, so its a bad method and hard to maintaining your code in the feature,
by this package you can see magic of implementation of1 queryString filters.

----------------------

##Steps:

###step1: 

first publish config file : 

```
php artisan vendor:publish
```

now in laravel config folder you can see filter-maker config file,
you can define your Filter
```

return [
    'FilterClassName' => [
       //inputs
        'field',
    ]
];

```



