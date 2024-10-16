<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Tests;

use Devscast\Flexpay\Client;
use Devscast\Flexpay\Data\TransactionType;
use Devscast\Flexpay\Exception\NetworkException;
use Devscast\Flexpay\Request\PayoutRequest;
use Devscast\Flexpay\Response\PayoutResponse;
use PHPUnit\Framework\TestCase;
use Devscast\Flexpay\Credential;
use Devscast\Flexpay\Data\Currency;
use Devscast\Flexpay\Data\Transaction;
use Devscast\Flexpay\Request\VposRequest;
use Devscast\Flexpay\Request\MobileRequest;
use Devscast\Flexpay\Response\VposResponse;
use Devscast\Flexpay\Response\CheckResponse;
use Devscast\Flexpay\Response\PaymentResponse;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

/**
 * Class ClientTest.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class ClientTest extends TestCase
{
    private function getFlexpay(callable|MockResponse $mock): Client
    {
        $flexpay = new Client(new Credential('token', 'ZONDO'));
        $reflection = new \ReflectionClass($flexpay);

        $http = $reflection->getProperty('http');
        $http->setAccessible(true);
        $http->setValue($flexpay, new MockHttpClient($mock));

        return $flexpay;
    }

    private function getResponse(string $file): MockResponse
    {
        return new MockResponse((string) file_get_contents(__DIR__ . '/fixtures/' . $file));
    }

    public function testVpos(): void
    {
        $flexpay = $this->getFlexpay($this->getResponse('vpos_ask.json'));
        $request = new VposRequest(
            amount: 1,
            reference: 'ref',
            currency: Currency::USD,
            callbackUrl: 'http://localhost:8000/callback',
            homeUrl: 'http://localhost:8000/home',
            approveUrl: 'http://localhost:8000/approve'
        );
        $response = $flexpay->vpos($request);

        $this->assertInstanceOf(VposResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('6708a4708d470_1728619632', $response->orderNumber);
        $this->assertEquals('https://beta-cardpayment.flexpay.cd/vpos/pay/6708a4708d470_1728619632', $response->url);
    }

    /**
     * @throws NetworkException
     */
    public function testPayout(): void
    {
        $flexpay = $this->getFlexpay($this->getResponse('payout.json'));

        $request = new PayoutRequest(
            amount: 10,
            reference: 'ref',
            currency: Currency::USD,
            callbackUrl: 'http://localhost:8000/callback',
            credentials: '',
            telephone: '243123456789',
            type: TransactionType::CARD
        );

        $response = $flexpay->payout($request);

        $this->assertInstanceOf(PayoutResponse::class,$response);
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('6708a4708d470_1728619632', $response->orderNumber);

    }
    public function testSuccessCheck(): void
    {
        $flexpay = $this->getFlexpay($this->getResponse('check_success.json'));
        $response = $flexpay->check('some_order_number');

        $this->assertInstanceOf(CheckResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertInstanceOf(Transaction::class, $response->transaction);
        $this->assertFalse($response->transaction->isSuccessful());
        $this->assertEquals('test', $response->transaction->reference);
    }

    public function testErrorCheck(): void
    {
        $flexpay = $this->getFlexpay($this->getResponse('check_error.json'));
        $response = $flexpay->check('not_found');

        $this->assertInstanceOf(CheckResponse::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertNull($response->transaction);
    }

    public function testMobile(): void
    {
        $flexpay = $this->getFlexpay($this->getResponse('mobile_success.json'));
        $request = new MobileRequest(
            amount: 10,
            reference: 'ref',
            currency: Currency::USD,
            callbackUrl: 'http://localhost:8000/callback',
            phone: '243123456789',
        );
        $response = $flexpay->mobile($request);

        $this->assertInstanceOf(PaymentResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('DtX9SmCYojWW243123456789', $response->orderNumber);
    }



    public function testHandleCallback(): void
    {
        /** @var array $data */
        $data = json_decode(file_get_contents(__DIR__ . '/fixtures/response_success.json'), true);
        $flexpay = $this->getFlexpay($this->getResponse('response_success.json'));

        $response = $flexpay->handleCallback($data);
        $this->assertInstanceOf(PaymentResponse::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('ZDN000003', $response->reference);
        $this->assertEquals('UBGC8s9L3VBm243815877848', $response->orderNumber);
    }
}
