# kraken-funding-notifier

Sends you a desktop notification on OS X, as soon as your Kraken account reaches a positive balance.

## Setup instructions
Install `terminal-notifier` via homebrew
    
    brew install terminal-notifier
Download the code

    git clone https://github.com/peaceman/kraken-funding-notifier.git
    cd kraken-funding-notifier
    composer install
Setup your API and currency settings in the `ENV_VARS` file. For example:

    API_KEY="<<< YOUR API KEY >>>"
    API_SECRET="<<< YOUR API SECRET >>>"
    CURRENCY="ZEUR"
