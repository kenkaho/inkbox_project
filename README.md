## Inkbox project app overview

I have installed the auth feature for Laravel to practice the auth flow, therefore it will ask to log in before you are able to hit the home page.

Create a new user, then it should redirect you to http://localhost:8000/profiles/1

At the user home page I have display the list of orders that created by the user, I have created the seeder to import 50 orders and order items with it,
therefore you should see 50 orders on this page.  Hit add orders to create additional order with the set products that created from the product seeder.  
The product's size is based on the assignment requirement.  Select the qty for the product you wish to add to the order.  0 qty is accpetable 
but the product will not add to the database.

Once submitted the order it will redirect the user back to the home page which is the order listing page.  On each order listing, 
it will include the order id, order total cost based on the product prices, and the list of products.  Hit the blue "Print Order" 
button to print the order.  When printing for the first time, it will create the print sheet and print sheet items based on the order items data.  Once the print sheet is generated the next time to view the sheet it will directly load the data previously generated to save the database resources.

The algorithm that generates the x_pos, y_pos, width, and height for each item happens when the "Print Order" button first hits.  I should have done a better job when writing this algorithm, it can be improved and make it more efficient with reducing the loops and make it less duplicate codes, I was running out of time to improve it, but it seems the algorithm works pretty well.  I may improve it in version 2.0 :)

For printing the Grid, I was trying to use vue.js to do it at the beginning but spent more than 30 minutes to try to get it working but no success so I use the 
CSS Grid system to draw the product boxes into the Grid instead.  But sometimes there is a problem with the size of the box when the first load, if it looks weird (overlapping) by reloading the page then it should fix the issues, the boxes may get resize but it should not overlap.


# Getting started

## Installation

Please check the official laravel installation guide for server requirements before you start. [Official Documentation](https://laravel.com/docs/5.4/installation#installation)


Clone the repository

    git clone https://github.com/kenkaho/inkbox_project.git

Switch to the repo folder

    cd print_order

Install all the dependencies using composer

    composer install

Copy the example env file and make the required configuration changes in the .env file

    cp .env.example .env

.env MySql database config example

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=<Database name>
    DB_USERNAME=root
    DB_PASSWORD=root

Run the database migrations (**Set the database connection in .env before migrating**)

    php artisan migrate
    
**Make sure you set the correct database connection information before running the migrations** [Environment variables](#environment-variables)

    php artisan migrate

## Database seeding

Run the database seeder

    php artisan db:seed

Start the local development server run the command line below in the print_order folder.

    php artisan serve

Go to the browser you should be able to hit the app with http://localhost:8000

**TL;DR command list**

    git clone https://github.com/kenkaho/inkbox_project.git
    cd print_order
    composer install
    cp .env.example .env 
    

***Note*** : It's recommended to have a clean database before seeding. You can refresh your migrations at any point to clean the database by running the following command

    php artisan migrate:refresh --seed
