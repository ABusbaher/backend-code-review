# Backend Code-Challenge

This is a dummy project, which is used to demonstrate knowledge of symfony and backend development in general.
It serves as an example with some bad practices included.

## Tasks

- [ ] Clone the repository or [download the code](https://github.com/cutlery42/backend-code-review/archive/refs/heads/main.zip)
  - [ ] Handle all [open issues](https://github.com/cutlery42/backend-code-review/issues) in the project
  - [ ] Make `vendor/bin/phpstan` pass without errors
  - [ ] Make `vendor/bin/phpunit` pass without errors
  - [ ] Upload the code to your own Repository (Avoid forking the repository and creating a PR, as this would make your solution visible to others)]

## Install

We prepared a dev environment with all dependencies included.
If this does not work / you're faster with your own setup, feel free to use your own environment.

1. Install [Nix](https://nixos.org/download) if you don't have it already.

NOTE (Windows only): if you are getting this error:

bin/console doctrine:database:create
/usr/bin/env: ‘php\r’: No such file or directory
error: Recipe `install` failed on line 3 with exit code 127

To fix this go to your project inside '/bin' folder and set UNIX format for console file instead of DOX:

- cd bin

- tr -d '\015' <console >console.new

- mv console console.old

- mv console.new console

2. Use `nix-shell` to enter the development environment
    - This will install all the necessary dependencies


## Development server

1. `just install` to install all dependencies
2. Run `just start` for a dev server (or `symfony serve` if you don't use `nix-shell`)
