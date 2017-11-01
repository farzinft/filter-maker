# filter-maker

when you need to make query string filters probably you are 
some of persons that use **if statement** to extend **Laravel QueryBuilder** to make your final query, so its a bad method and hard to maintaining your code in the feature,
by this package you can see magic of implementation of queryString filters.

----------------------

## step1 ##
install:
> composer require farzin/filter-maker:"dev-master"

Add service provider:

> Farzin\FilterMaker\FilterMakerProvider::class

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

now in app directory you can see Filters directory and UserFilter class. you see methods that created for each one of inputs, this is place which you can define your query builder statement:
example:

    
    public function firstName($value)
	{
		$this->builder->where('first_name', $value);
	}

	public function lastName($value)
	{
		$this->builder->where('last_name', $value);
	}

## step3 ##

make a custom service provider :

> php artisan make:provider SampleServiceProvider

and add this provider in your app.php config file. we have to defer loading of this provider because we don't need to load this provider in every request. 
we use laravel Contextual binding to bind builder to our filter class,
read more: [Contextual binding ](https://laravel.com/docs/5.5/container#contextual-binding)

**a sample provider** :

	    protected $defer = true;
        public function register()
    {
        $this->app->when(UserFilter::class)->needs(Builder::class)->give(function () {
            return #query;
        });
    }

    public function provides()
    {
        return [
         UserFilter::class
        ];
    }


now in your Controller method inject filter and use **applyFilter()** method now  you have filtered query ! that's it! , you can use this builder instance in datatable...
example:

    
    public function userList(Request $request, App\Filters\UserFilter $userFilter) 
    {
	    $query = $userFilter->applyFilter(); // Builder Instance
    }
	


> any time you can add new queryString variables to config file, just add its to array and run artisan command

