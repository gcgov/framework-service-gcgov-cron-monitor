# CLAUDE.md — gcgov/framework-service-gcgov-cron-monitor

A **framework-service plugin** for `gcgov/framework`. Read the framework's own `CLAUDE.md` first for the
plugin/lifecycle model — this file only covers what this plugin adds. See `README.md` for prose.

> Internal to Garrett County Government. It reports cron runs to a separate cron-monitor web service.

## Purpose
A tiny **client library** (no routes, no controllers) that records the **start and end** of a long-running
cron/CLI task by calling an external cron-monitor API. Use it to wrap scheduled tasks so their execution is
tracked centrally.

Namespace / PSR-4: `gcgov\framework\services\cronMonitor\` → `src/` (single class `cronMonitor.php`). Composer
type: `framework-service`. Depends on `guzzlehttp/guzzle`.

## Install & register
```php
// \app\app::registerFrameworkServiceNamespaces()
return [ '\gcgov\framework\services\cronMonitor' ];
```
Registration is optional for use (the class works if merely autoloaded), but keep it consistent with the other
services. Configure the monitor endpoint in `environment.json → appDictionary`:
```json
{ "appDictionary": { "cronMonitorUrl": "https://apps.garrettcounty.local/cron-monitor/" } }
```

## Usage
```php
$cronMonitor = new \gcgov\framework\services\cronMonitor\cronMonitor( $cronJobId );
// ... perform the long-running cron task ...
$cronMonitor->end();
```
- The **constructor** fires an async Guzzle `GET jobHistory/start/{jobId}` against `cronMonitorUrl` (base URI),
  so the "start" ping overlaps your work rather than blocking it.
- `end()` awaits the start response to capture the `data` run id, then calls
  `GET jobHistory/end/{jobId}/{runId}`.

## Design notes / gotchas
- **Fail-safe / non-throwing**: all HTTP and JSON errors are swallowed (empty `catch`). Monitoring never breaks
  the underlying job, but a misconfigured `cronMonitorUrl` fails silently — verify the URL if runs aren't
  showing up in the monitor.
- `jobId` is caller-supplied and must match a job configured in the cron-monitor service.
- `end()` still pings even if the start ping failed (with an empty `runId`).
- Typically invoked from a `'CLI'` route/controller (see the framework `CLAUDE.md` on CLI tasks).

## When editing this plugin
- Lowercase class/file names. Preserve the async-start / awaited-end pattern and the fail-safe error handling.
- If adding config, prefer `environment.json → appDictionary` keys over new required config. `composer ci`
  before pushing.
