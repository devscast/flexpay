<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Tests;

use Devscast\Flexpay\Environment;
use PHPUnit\Framework\TestCase;

/**
 * Class EnvironmentTest.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class EnvironmentTest extends TestCase
{
    private Environment $dev;

    private Environment $prod;

    protected function setUp(): void
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
            $this->prod->getCardPaymentUrl()
        );
        $this->assertEquals(
            'https://beta-cardpayment.flexpay.cd/v1.1/pay',
            $this->dev->getCardPaymentUrl()
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

    public function testGetPayoutUrl(): void
    {
        $this->assertEquals(
            'https://backend.flexpay.cd/api/rest/v1/merchantPayOutService',
            $this->prod->getPayoutUrl()
        );

        $this->assertEquals(
            'https://beta-backend.flexpay.cd/api/rest/v1/merchantPayOutService',
            $this->dev->getPayoutUrl()
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
