#!/usr/bin/env bash
if [ -f ENV_VARS ]; then
	. ENV_VARS
fi

export API_KEY
export API_SECRET
export CURRENCY

exec php kraken-funding-notifier.php
