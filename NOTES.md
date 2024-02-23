# App info

This is a simple API to run a Tic Tac Toe game.

To run the application, you can use `./vendor/bin/sail up`

# Exercise Notes

Here I'm tracking the process I'm following and the decisions I'm taking.

1. Following [doc](https://laravel.com/docs/10.x/installation#docker-installation-using-sail) I setup Laravel locally.
   - Why I used Sail? Because it makes scaffolding of a new app very quick and easy locally. In a real-world project, I would more carefully consider if a custom Docker is better.
   - In a real-world project, I would have also invested time in fixing the default docker-compose to properly handle a volume for `vendor` folder, etc..
   - For the same reason, I didn't create a Makefile, which is something I usually do to create a collection of simplified command to run in the container

