<?php

declare(strict_types=1);

namespace gcgov\framework\services\cronMonitor\tests\Unit;

use PHPUnit\Framework\TestCase;
use gcgov\framework\services\cronMonitor\cronMonitor;

final class CronMonitorSmokeTest extends TestCase {

	public function testClassExists(): void {
		$this->assertTrue( class_exists( cronMonitor::class ) );
	}

	public function testPublicApiShape(): void {
		$reflection = new \ReflectionClass( cronMonitor::class );
		$this->assertTrue( $reflection->hasMethod( '__construct' ) );
		$this->assertTrue( $reflection->hasMethod( 'end' ) );

		$constructor = $reflection->getMethod( '__construct' );
		$this->assertCount( 1, $constructor->getParameters() );
		$this->assertSame( 'jobId', $constructor->getParameters()[0]->getName() );

		$end = $reflection->getMethod( 'end' );
		$this->assertSame( 'void', (string) $end->getReturnType() );
	}

}
