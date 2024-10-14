<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Tests;

use PHPUnit\Framework\TestCase;
use Devscast\Flexpay\Environment;

/**
 * Class EnvironmentTest.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class EnvironmentTest extends TestCase
{
    private Environment $dev;
    private Environment $prod;

    public function setUp(): void
    {
        $this->dev = Environment::SANDBOX;
        $this->prod = Environment::LIVE;
    }

    public function testEnvironment(): void
    {
        $this->assertEquals('prod', $this->prod->value);
        $this->assertEquals('dev', $this->dev->value);
        $this->assertEquals(Environment::LIVE, Environment::from('prod'));
        $this->assertEquals(Environment::SANDBOX, Environment::from('dev'));
    }

    public function testGetCardPaymentUrl(): void
    {
        $this->assertEquals(
            'https://cardpayment.flexpay.cd/v1.1/pay',
            $this->prod->getVposAskUrl()
        );
        $this->assertEquals(
            'https://beta-cardpayment.flexpay.cd/v1.1/pay',
            $this->dev->getVposAskUrl()
        );
    }

    public function testGetMobilePaymentUrl(): void
    {
        $this->assertEquals(
            'https://backend.flexpay.cd/api/rest/v1/paymentService',
            $this->prod->getMobilePaymentUrl()
        );
        $this->assertEquals(
            'https://beta-backend.flexpay.cd/api/rest/v1/paymentService',
            $this->dev->getMobilePaymentUrl()
        );
    }

    public function testGetCheckStatusUrl(): void
    {
        $this->assertEquals(
            'https://backend.flexpay.cd/api/rest/v1/check/123456',
            $this->prod->getCheckStatusUrl('123456')
        );
        $this->assertEquals(
            'https://beta-backend.flexpay.cd/api/rest/v1/check/123456',
            $this->dev->getCheckStatusUrl('123456')
        );
    }
}
