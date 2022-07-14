This Rest API project with Laravel gives you the data of the product purchase information :
eg. product details, categories, transactions and users (buyer/seller).


The transformer is set by using fractal (https://fractal.thephpleague.com/transformers/) so that the table columns can be adjusted to the name which is user friendly. 

There are two authorization methods (auth:api and client:credentials) which is made by using Passport. 


It is possible to read, create, edit and delete any data, so depending on the authorization, there is a limit in doing so.

To be able to see all the Client ID's per user, there is a front end which is made by Vue.js. Here you can also create the OAuth Clients as well.


To be able to test the API, the factories and seeder are filled in, you only need to type:

```
php artisan db:seed
```

