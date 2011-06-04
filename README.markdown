Patrick's Awesome Toolset
================
Version  3.0  
by [Patrick McKinley] [homepage]  
[http://www.patrick-mckinley.com/] [homepage]  

Installation
-----
 * Put code on PHP5.3 enabled server with Mcrypt
 * Open bootstrap.php in a text editor
    * Put database details into the DB::configure() function
    * Add a random encryption key to the Mcrypt declaration

Twitter Module Usage
-----
 * Install the twitter module  
        ./cafe Twitter install
 * Add twitter account  
        ./cafe Twitter add
 * Post a tweet - the account needs to be added with the above command  
        ./cafe Twitter tweet <account> <tweet>
    * eg:  
            ./cafe Twitter tweet lilmuckers "Posting a tweet"

Xbox Live Module Usage
-----
 * Install the XboxLive module  
        ./cafe XboxLive install
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
 * Add a notification hook
        ./cafe XboxLive addlocation <gamertag> <type> <identifier> <custom prefix> <custom suffix>
    * gamertag - The gamertag to add this to
    * type - There is, at the moment, only one valid notification hook
       * twitter
    * identifier - Unique identifier for a registered account in the notification system. For twitter this would be the twitter username.
    * Suffixes and Prefixes are put on the beginning and end of the updates, to include custom hashtags for the twitter calls, for example.
    * eg:
            ./cafe XboxLive addlocation Lilmuckers twitter lilmuckers "#xbox" "See the full list on http://bit.ly/myxbox"
       * This will make Lilmuckers account tweet to the lilmuckers twitter feed, prefixing and suffixing the tweet accordingly.
 * Fire off the notification hooks
        ./cafe XboxLive notify <gamertag> <timeframe>
    * gamertag - optional - if unspecified all registered gamertags will be used.
    * timestamp - optional - format recognised by the [PHP strtotime function] [strtotime] - if unspecified defaults to the last hour ("-1 hour")
 * Load achievements for a game from downloaded HTML file
        ./cafe XboxLive file <game-slug> <file>
    * This is very basic - and won't work on multi-gamertag environments.
    * Built for my own reasons - since MicroSoft made it practically impossible to scrape XboxLive.

[strtotime]: http://php.net/strtotime/  "PHP strtotime"
[homepage]: http://www.patrick-mckinley.com/ "Patrick McKinley - Magento PHP Developer"
