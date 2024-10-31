<p align="center">News Aggregator Laravel Project</p>

## How to use
This application setup with docker with Laravel sail. 
1.  Take pull inside WSL or Ubuntu. // you should have Ubuntu setup in your windows system
2.  Make sure you have desktop docker setup in your system.
2.  Go to project root folder and run "./vendor/bin/sail build".  // wait until build is complete it take some time
3.  Run "./vendor/bin/sail up" // you can see now continer and images in desktop docker
4.  Now you can access project in - http://localhost:8083
5.  Run "./vendor/bin/sail artisan migrate". // it will create all necessory tables
6.  Run "./vendor/bin/sail artisan db:seed". // it will create required data for use.
7.  Run "./vendor/bin/sail artisan schedule:run". // it will fetch articles from (Guardian API,News API,NewYork Time API) given APIs.
8. Now you can go through the document and to check ALL APIs and related details.

## Project Informations
1.  Site URL - http://localhost:8083
2.  API URL - http://localhost:8083/api
3.  Document URL - http://localhost:8083/api/documentation
4.  Test case code - "./vendor/bin/sail artisan test"
