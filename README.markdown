XboxLive Scraper
================
Version  2.0 
by Patrick McKinley
http://www.patrick-mckinley.com/

Installation
-----
 * Put code on PHP5.2 enabled server with Mcrypt
 * Open bootstrap.php in a text editor
    * Put database details into the DB::configure() function
    * Add a random encryption key to the Mcrypt declaration
 * Run the following command
        ./cafe XboxLive install

Twitter Module Usage
-----
 * Add twitter account
        ./cafe Twitter add
 * Post a tweet - the account needs to be added with the above command
        ./cafe Twitter tweet <account> <tweet>
    * eg:
            ./cafe Twitter tweet lilmuckers "Posting a tweet"

Xbox Live Module Usage
-----
 * Add User
        ./cafe XboxLive add <gamertag> <passport> <password>
    * eg:
            ./cafe XboxLive add Lilmuckers username@live.com password
    * This also supports interactive input by just calling it in the format
            ./cafe XboxLive add <gamertag>
 * Change User Account
        ./cafe XboxLive edit <gamertag> <passport> <password>
    * eg:
            ./cafe XboxLive edit Lilmuckers username@live.co.uk password1
    * This also supports interactive input by just calling it in the format
            ./cafe XboxLive edit <gamertag>
 * Update all accounts score data
        ./cafe XboxLive update
 * Update score data for a specific account
        ./cafe XboxLive update <gamertag>
    * eg:
            ./cafe XboxLive update Lilmuckers
 * Check consistency of user data
        ./cafe XboxLive check <gamertag> 
    * eg:
            ./cafe XboxLive check Lilmuckers
 * Force update of all information on user
        ./cafe XboxLive force <gamertag>
    * eg:
            ./cafe XboxLive force Lilmuckers
 * Delete a given gamertag from the DB with all data
        ./cafe XboxLive delete <gamertag>
    * eg:
            ./cafe XboxLive delete Lilmuckers
