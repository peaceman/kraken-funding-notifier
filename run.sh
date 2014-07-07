#!/usr/bin/env bash
if [ -f ENV_VARS ]; then
	. ENV_VARS
fi

# the ENV_VARS file has to contain and fill these variables
export API_KEY
export API_SECRET
export CURRENCY

exec php kraken-funding-notifier.php
