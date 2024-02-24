# App info

This is a simple API to run a Tic Tac Toe game.

- To run the application, you can run the command: `./vendor/bin/sail up`
- To execute the tests, you can run the command `php artisan test`
- To run migrations, you can run the command `./vendor/bin/sail artisan migrate`

## How to play a game

DB hostname: `https://tic-tac-toe-laravel.vercel.app/` - just replace the hostname in the following cURL commands to play in production.

### cURL commands

- Start a game: `curl -XPOST -H "Content-type: application/json" 'http://localhost/api/new-game'`
- Make a move: `curl -XPOST -H "Content-type: application/json" -d '{
  "gameId": 1,
  "player": "X",
  "position": 0
  }' 'http://localhost/api/move'`

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
   - I decided to run migration from local env for simplicity because I didn't want to lose too much time in finding a tricky way to do it since Vercel doesn't allow for php commands execution and having it deployed is a plus
   - To make a concrete implementation, I need:
     - A migration to create the `games` table - the table only needs an ID basically, atm; I will also add created_at and updated_at as best practice - I will face the problem 1 step at a time so I don't care about next requirements for now
     - The Model for the new table - I will create it together with migration with the command `php artisan make:model Game --migration`
     - The business logic of creating a new record in the table to create a new game, and return the ID
     - Test list:
       - create new record and return ID
       - throw a custom Exception if something goes wrong --> to do this I need to first extract the DB logic, for example in a Repository class, to then mock it and force an exception coming from there
   - I created GameRepository to contain all the logic about working with the Game persistance, in our case it's DB persistance layer
   - Usually, I would follow Object Calisthenics rules and avoid sharing primitives variables, for example ID shouldn't be an int but a Data Class - here I decided to favor simplicity and stick with the int
   - I used Parallel change here: I first created GameRepository and its Database implementation to encapsulate the DB writing logic (see git log for evidence) and then I replaced it in the service - this way the refactoring never broke the existing code
   - As visible in GIT logs, after introducing the repository, I can also easily test in the service that if something break up with the repo, we throw a custom exception - I like custom exception because they are easily readable

3. Finally, following Trunk-Based Development coding pattern of [Keystone Interface](https://martinfowler.com/bliki/KeystoneInterface.html), I add the route to access the API as last piece - this would have allowed me to continuously release my progress hiding it to the user. It's the easier pattern to allow TBD and CI/CD.

At this point, I already setup some architecture and will stick to this, so following notes wll probably be smaller and strictly related to the assignment to finish the exercise.

## Step 3: Create the API to make a move

Requirement: Need an endpoint to call to play a move in the game. The endpoint should take as inputs the Game ID (from the first endpoint), a player number (either 1 or 2), and the position of the move being played. 
    The response should include a data structure with the representation of the full board so that the UI can update itself with the latest data on the server. 
    The response should also include a flag indicating whether someone has won the game or not and who that winner is if so.

1. To start accepting moves, we need to represent the board. I will make this representation as easy as possible: an array from 0 to 8 will represent each coordinate of the board. 
    For a bigger/more complex board, a matrix of coordinates might be fit best, but here we can do things more easily. I will store the array in a column `status` of the `game` table.
    This is the same array I will return to represent the board to frontend.

    BOARD:
    0 | 1 | 2 
    3 | 4 | 5
    6 | 7 | 8

    I also have to handle the players - we might ask for a name and save it, but here we will just call them Player X and Y.
    Some examples: 
        - empty board =                     [0,1,2,3,4,5,6,7,8]
        - player one moved in position 2 =  [0,1,X,3,4,5,6,7,8]
        - player two moved in position 4 =  [0,1,X,3,Y,5,6,7,8]

2.  To decide who and if they won, I will build a logic checking all the possibilities:
    - rows: [ [0, 1, 2], [3, 4, 5], [6, 7, 8] ]
    - columns: [ [0, 3, 6], [1, 4, 7], [2, 5, 8] ]
    - diagonals: [ [0, 4, 8], [2, 4, 6] ]
    Once one of those as all equals symbols, the player won. If the board has no left space, no one wins and the return field will be empty.
    I will also store the winner in a column of the table, and add a timestamp column to store the end of the game timestamp.

Here I realized that freemysqlhosting - the hosting for Mysql I was using, is stuck at 5.5 so no JSON columns... I turned that into a string then.

## Step 4: Handle errors in making a move

Requirement: The endpoint that handles moves being played should perform some basic error handling to ensure the move is valid, and that it is the right players turn
    (ie. a player cannot play two moves in a row, or place a piece on top of another player’s piece)

Note: Formal validation will be done with validated Requests from Laravel. Business logic validation will be in my code.
I will limit formal validation to the request and then trust that in internal classes - I know is not ideal, ideally I would have created data classes / value objects and validate data more strictly in internal pieces.
This is just to simplify the exercise.

1. Move must be valid
   - position must be between 0 and 8 [v] -> request validation
   - position must be free [v]
   - player cannot make 2 moves in a row (player X will start by default)

## Step 4b: Refactoring

I spend time in setting up the walking skeleton and to commit decently in the first steps to show the approach, therefore at some point I decided to be more pragmatic in the implementation.
Thanks to tests, I refactored a bit after that, before building the tests examples - but of course there might probably be something more to clean up or refactor for an ideal code design (if there can be an ideal one).

## Step 5: Allow to play somehow

Requirement: Please provide a test case example (such as test cases or a list of cURL commands) of a fully played out game with your solution.

1. Feature test simulating a game via HTTP Requests -> Feature/GameSimulationTest.php, run `./vendor/bin/sail artisan test --filter GameSimulationTest` 

2. cURL requests with instructions to play in production or locally -> see beginning of this file for the commands to start a new game and make a move

3. CLI command for interactive game -> run `./vendor/bin/sail artisan tictactoe`

## Final notes

- I usually also use custom Response objects that can easily implements Arrayable to expose how the JSON needs to be done and encapsulate the responses, same as for Requests encapsulating params and validation.
- Again, I want to emphasize that I would never keep using primitives and native arrays, I would create value objects/ data class to handle data structures
- I usually also add comments in Models to be able to use attributes around withouth PHP storm warning me that they don't exists
- Ideally I should test all the possible winning options, I will only test one for each situation (rows, columns and diagonals) as a proof
- I treated Repository only as container for data persistance logic - all the decisions about the game are takes from the service
- Some casting is handled manually to avoid DB connection to trigger from Laravel and still be able to write a unit test
- At some point I decided to take some more simplifications to keep the exercise time-boxed to 4-6 hours
- I didn't care about performance at all - usually performance are a problem to solve only when it really happens, in this case it didn't even when finished

About the command: I've not created much command line tools in my life, it was funny, but the code for printing is not ideal :D but I decided it was enough to make it work, it was just to give a simple way to test the implementation works.
Thanks to my approach, following a Clean Arch, the Command is a replacement of Controller: Command is for CLI, Controller is for HTTP requests. The services and repositories are the same.

Of course, refactoring could be infinite - I decided to stop at some point.
