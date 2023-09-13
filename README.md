# Cron Monitor Service

## Primary purpose
Adds cron monitor service to gcgov/framework apps

## Installation
1. `composer require gcgov/framework-service-gcgov-cron-monitor`
2. Add service namespace to `/app/app.php` method `registerFrameworkServiceNamespaces`: `gcgov\framework\services\cronMonitor`. Ex:
    ```php
	public function registerFrameworkServiceNamespaces(): array {
		return [
			'\gcgov\framework\services\cronMonitor'
		];
	}
    ```
4. Add cron monitor api url to `environment.json` in `appDictonary.cronMonitorUrl`. Ex:
    ```json
    "appDictionary": {
      "cronMonitorUrl": "https://apps.garrettcounty.local/cron-monitor/"
    }
    ```

## Usage
```php
$cronMonitor = new \gcgov\framework\services\cronMonitor\cronMonitor( $cronJobId );
//perform long running cron task
$cronMonitor->end();
```
