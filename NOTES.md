# App info

This is a simple API to run a Tic Tac Toe game.

- To run the application, you can run the command: `./vendor/bin/sail up`
- To execute the tests, you can run the command `php artisan test`

# Exercise Notes

Here I'm tracking the process I'm following and the decisions I'm taking. I did it here to leave the default README file unchanged.

## Step 1: Walking Skeleton

1. Following [doc](https://laravel.com/docs/10.x/installation#docker-installation-using-sail) I setup Laravel locally.
   - Why I used Sail? Because it makes scaffolding of a new app very quick and easy locally. In a real-world project, I would more carefully consider if a custom Docker is better.
   - In a real-world project, I would have also invested time in fixing the default docker-compose to properly handle a volume for `vendor` folder, etc..
   - For the same reason, I didn't create a Makefile, which is something I usually do to create a collection of simplified command to run in the container
2. Laravel already scaffold a default home page, so I only had to setup a pipeline deploy the app - I will set this up serverless
   - I used Vercel for a serverless deployment because it's easy and free :)
   - I used Freemysqlhosting because - well, it's free :D
   - Articles references: [this](https://calebporzio.com/easy-free-serverless-laravel-with-vercel) and [this](https://debjit012.medium.com/deploy-laravel-on-vercel-with-database-image-storage-and-email-2d3917cfc914)
     - To make it work after the article, I had to: 
       - upgrade php-vercel version to 0.7.0
       - set the build folder on Vercel to `api`
3. I used Vercel default auto deploy functionality, but I also want to show something more about my habits and skills here; that's why I created a GitHub Action configured to build the app, static analysis, and tests; in a real-world project, I would have invested time in understanding how to trigger the release on Vercel from here (I tried, but manual deploy from Actions to Vercel with PHP had some issues and I didn't want too lose to much time on that since it's not the focus). Of course, this is not ideal because build & test is happening in parallel to deploy - they should be one after another so that I only deploy if tests and other automated checks are fine. In that case, I typically like to have a staging release triggered at every push on master, while Prod release happens after a tag. I will probably use Github actions later for migrations for simplicity.  

## Step 2: Create the first API

Requirement: Need an endpoint to call to start a new game. The response should give me some kind of ID for me to use in other endpoints calls to tell the backend what game I am referring to.

Approach: I will work in TDD outside-in, so I will build this from the outside (Controller) to the inside (-> Services -> DB). 
    I will try to make the first steps stricyl respecting TDD committing at every baby step of TDD so that is visibile in GIT LOG that I did TDD.

1. Create an invokable NewGameController.
