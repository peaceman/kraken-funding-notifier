<?php
require_once __DIR__ . '/vendor/autoload.php';
use Symfony\Component\Process\ProcessBuilder;

$apiKey = getenv('API_KEY');
$apiSecret = getenv('API_SECRET');
$currency = getenv('CURRENCY');

$kraken = new Kraken($apiKey, $apiSecret);
$krakenFundingObserver = new KrakenFundingObserver($kraken, $currency);
$krakenFundingObserver->run();

class KrakenFundingObserver
{
	const POLL_INTERVAL_IN_SECONDS = 10;

	protected $apiClient;
	protected $currency;
	protected $lastFunding;
	protected $notificationProcessBuilder;

	public function __construct(Kraken $apiClient, $currency)
	{
		$this->apiClient = $apiClient;
		$this->currency = $currency;
		$this->notificationProcessBuilder = new ProcessBuilder();
		$this->notificationProcessBuilder->setPrefix('terminal-notifier');
	}

	public function run()
	{
		while (true) {
			$this->checkFunding();
			sleep(static::POLL_INTERVAL_IN_SECONDS);
		}
	}

	protected function checkFunding()
	{
		$currentFunding = $this->queryApi();

		if (!is_null($this->lastFunding) && $currentFunding > $this->lastFunding) {
			$this->createNotification($currentFunding);
		}

		$this->lastFunding = $currentFunding;
	}

	protected function queryApi()
	{
		$response = $this->apiClient->QueryPrivate('Balance');
		if (!empty($response['error'])) {
			$msg = 'An error occurred while querying the Kraken API. (' . implode('; ', $response['error']) . ')';
			throw new RuntimeException($msg);
		}

		$result = $response['result'];
		if (!isset($result[$this->currency])) {
			$msg = "The defined currency ($this->currency) is not in the received balance list.";
			throw new RuntimeException($msg);
		}

		return (float)$result[$this->currency];
	}

	protected function createNotification($newBalance)
	{
		$title = "Your funding on Kraken arrived!";
		$msg = "Old balance: $this->lastFunding $this->currency; New balance: $newBalance $this->currency";

		$this->notificationProcessBuilder
			->setArguments(['-title', $title, '-message', $msg, '-sound', 'default'])
			->getProcess()
			->run();
	}
}
