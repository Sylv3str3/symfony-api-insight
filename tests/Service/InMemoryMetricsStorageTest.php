<?php

namespace ApiInsight\Tests\Service;

use ApiInsight\Service\InMemoryMetricsStorage;
use PHPUnit\Framework\TestCase;

class InMemoryMetricsStorageTest extends TestCase
{
    private InMemoryMetricsStorage $storage;
    
    protected function setUp(): void
    {
        $this->storage = new InMemoryMetricsStorage();
    }
    
    public function testRecordApiCall(): void
    {
        $this->storage->recordApiCall('test_route', 200, 0.1);
        $metrics = $this->storage->getMetrics();
        
        $this->assertArrayHasKey('test_route', $metrics);
        $this->assertEquals(1, $metrics['test_route']['total_calls']);
        $this->assertEquals(0.1, $metrics['test_route']['avg_duration']);
        $this->assertEquals(0.1, $metrics['test_route']['min_duration']);
        $this->assertEquals(0.1, $metrics['test_route']['max_duration']);
        $this->assertArrayHasKey('200', $metrics['test_route']['status_codes']);
        $this->assertEquals(1, $metrics['test_route']['status_codes']['200']);
        $this->assertEquals(0, $metrics['test_route']['errors']);
        $this->assertEquals(0, $metrics['test_route']['error_rate']);
    }
    
    public function testRecordMultipleApiCalls(): void
    {
        $this->storage->recordApiCall('test_route', 200, 0.1);
        $this->storage->recordApiCall('test_route', 200, 0.3);
        $this->storage->recordApiCall('test_route', 404, 0.2);
        
        $metrics = $this->storage->getMetrics();
        
        $this->assertEquals(3, $metrics['test_route']['total_calls']);
        $this->assertEquals(0.2, $metrics['test_route']['avg_duration']);
        $this->assertEquals(0.1, $metrics['test_route']['min_duration']);
        $this->assertEquals(0.3, $metrics['test_route']['max_duration']);
        $this->assertEquals(2, $metrics['test_route']['status_codes']['200']);
        $this->assertEquals(1, $metrics['test_route']['status_codes']['404']);
        $this->assertEquals(1, $metrics['test_route']['errors']);
        $this->assertEquals(33.33, $metrics['test_route']['error_rate']);
    }
    
    public function testGetRouteMetrics(): void
    {
        $this->storage->recordApiCall('route1', 200, 0.1);
        $this->storage->recordApiCall('route2', 500, 0.2);
        
        $metrics = $this->storage->getRouteMetrics('route1');
        
        $this->assertEquals(1, $metrics['total_calls']);
        $this->assertEquals(0.1, $metrics['avg_duration']);
        $this->assertEquals(0, $metrics['errors']);
        
        $metrics = $this->storage->getRouteMetrics('route2');
        
        $this->assertEquals(1, $metrics['total_calls']);
        $this->assertEquals(0.2, $metrics['avg_duration']);
        $this->assertEquals(1, $metrics['errors']);
        $this->assertEquals(100, $metrics['error_rate']);
    }
    
    public function testReset(): void
    {
        $this->storage->recordApiCall('test_route', 200, 0.1);
        $this->storage->reset();
        
        $metrics = $this->storage->getMetrics();
        
        $this->assertEmpty($metrics);
    }
} 