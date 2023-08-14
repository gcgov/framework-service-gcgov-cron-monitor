<?php
namespace gcgov\framework\services\cronMonitor;


use gcgov\framework\config;
use GuzzleHttp\Exception\GuzzleException;

class cronMonitor {

	private string                      $jobId;
	private string                      $runId;
	private \GuzzleHttp\Client          $client;
	private \GuzzleHttp\Promise\Promise $jobPromise;

	public function __construct( string $jobId ) {
		$this->jobId      = $jobId;
		$config           = config::getEnvironmentConfig();
		$this->client     = new \GuzzleHttp\Client( [ 'base_uri' => $config->appDictionary[ 'cronMonitorUrl' ] ] );
		$this->jobPromise = $this->client->requestAsync( 'GET', 'jobHistory/start/' . $this->jobId );
	}

	public function end(): void {

		//make sure the job started and get the run id from it's response
		$response = $this->jobPromise->wait();
		$runId    = '';
		try {
			$parsedResponse = json_decode( $response->getBody(), false, 512, JSON_THROW_ON_ERROR );
			$runId          = $parsedResponse->data;
		}
		catch( \JsonException|\Exception $e ) {
		}

		//end the job regardless of whether we got a successful start or not
		try {
			$this->client->request( 'GET', 'jobHistory/end/'.$this->jobId.'/' . $runId );
		}
		catch( \Exception|GuzzleException $e ) {
		}
	}

}
