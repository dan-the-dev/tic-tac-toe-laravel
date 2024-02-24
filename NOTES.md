# App info

This is a simple API to run a Tic Tac Toe game.

- To run the application, you can run the command: `./vendor/bin/sail up`
- To execute the tests, you can run the command `php artisan test`
- To run migrations, you can run the command `./vendor/bin/sail artisan migrate`

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

Approach: I will work in TDD outside-in, so I will build this from the outside (Controller) to the inside (-> Services -> DB). This will not be visible, because with this approach the architecture would be iterative and therefore very simple for such simple exercise.
        Since I want also to show some other skills, such as CQRS approach, a bit of hexagonal arch, etc - I will not focus on strict TDD, but from GIT log it should be somehow a bit visible.

Note: I will use this first step as showcase for some of the practices and metholodiges I use such as TDD, manual fakes and mocks, CQRS, etc - following steps I will be a bit faster following the decisions I already took in this step.

1. Create an invokable NewGameController.
   - Controller has no Request object because it has no input params.
   - I put the services in the `services` folder following Laravel default infra, I typically prefer to modularize by domain elements (NewGame could have been a module here) and then under each module have the infra components, ideally following Clean Arch - it wasn't worth for the exercise)
   - I organized Services in Commands following CQS/CQRS, which I usually do when complexity is enough - I did it here to showcase this. We will probably have only Commands in this exercise, btw.
   - I made a refactor of the controller immediately because strict TDD with triangulation etc would have costed a lot just to force a simple value on a simple behavior. The controller just have to validate request, send request to service, and handle response imho - and in this case we don't have a request and response is very small, so it wasn't worth it.
   - Since the Response here is very easy, I didn't create an adapter class for the response creation, but I created a dedicated method to isolate the responsibility anyway.
   - Important notes about tests: I typically favor manual Fakes and Spies over mocking library because this makes tests not coupled with the implementation (calling a specific method of a collaborator is an implementation detail) and therefore easier to maintain and doesn't change if I change the way those two classes communicate (the fake will change, but the fake is not specific of a single test - see [here](https://antodippo.com/how-can-i-trust-my-testsuite/#/25) for a reference).

2. Implement the concrete of `NewGameCommandHandler` that handle the real behavior.
   - Here I can try a more strict approach to TDD, GIT log should make it visible enough
   - This strict approach will lead me to implement everything in the service, and then I will extract new components
   - To make a concrete implementation, I need:
     - A migration to create the `games` table - the table only needs an ID basically, atm; I will also add created_at and updated_at as best practice - I will face the problem 1 step at a time so I don't care about next requirements for now
     - The Model for the new table - I will create it together with migration with the command `php artisan make:model Game --migration`
     - The business logic of creating a new record in the table to create a new game, and return the ID
     - Test list:
       - create new record and return ID
       - throw a custom Exception if something goes wrong
