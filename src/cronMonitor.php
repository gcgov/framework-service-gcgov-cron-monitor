<?php
namespace gcgov\framework\services\cronMonitor;


use gcgov\framework\config;
use GuzzleHttp\Exception\GuzzleException;

class cronMonitor {

	private string                               $jobId;
	private \GuzzleHttp\Client                   $client;
	private \GuzzleHttp\Promise\PromiseInterface $jobPromise;

	public function __construct( string $jobId ) {
		$this->jobId      = $jobId;
		$config           = config::getEnvironmentConfig();
		$this->client     = new \GuzzleHttp\Client( [ 'base_uri' => (string) ( $config->appDictionary[ 'cronMonitorUrl' ] ?? '' ) ] );
		$this->jobPromise = $this->client->requestAsync( 'GET', 'jobHistory/start/' . $this->jobId );
	}

	public function end(): void {

		$runId = '';
		//make sure the job started and get the run id from it's response
		try {
			$response       = $this->jobPromise->wait();
			$parsedResponse = json_decode( (string) $response->getBody(), false, 512, JSON_THROW_ON_ERROR );
			if( is_object( $parsedResponse ) && isset( $parsedResponse->data ) ) {
				$runId = (string) $parsedResponse->data;
			}
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
