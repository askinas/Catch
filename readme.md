## Installation

- Clone to local `git clone https://github.com/askinas/Catch.git`
- Change directory to `Catch` folder.
- Run `Composer Install`.


## Running App
Order:process Command can receive 2 parameters, output filename and filetype.
If no parameters given the output will be catch.csv.

To run the programm use this command: `php artisan order:process`.

To change the output filename and type use this command:
`php artisan order:process filename type`

For example: `php artisan order:process export csv`


Everytime the app is run, it will create a temporary storage file named catchxxx.tmp