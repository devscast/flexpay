<?php

declare(strict_types=1);

namespace Devscast\Flexpay;

use Devscast\Flexpay\Exception\NetworkException;
use Devscast\Flexpay\Request\CardRequest;
use Devscast\Flexpay\Request\MobileRequest;
use Devscast\Flexpay\Request\PayoutRequest;
use Devscast\Flexpay\Request\Request;
use Devscast\Flexpay\Response\CardResponse;
use Devscast\Flexpay\Response\CheckResponse;
use Devscast\Flexpay\Response\FlexpayResponse;
use Devscast\Flexpay\Response\PaymentResponse;
use Devscast\Flexpay\Response\PayoutResponse;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Component\HttpClient\RetryableHttpClient;
use Symfony\Component\PropertyInfo\Extractor\ConstructorExtractor;
use Symfony\Component\Serializer\Normalizer\BackedEnumNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Class Client.
 *
 * @author bernard-ng <bernard@devscast.tech>
 */
final class Client
{
    private HttpClientInterface $http;

    private Serializer $serializer;

    public function __construct(
        public readonly Credential $credential,
        public readonly Environment $environment = Environment::SANDBOX,
    ) {
        $this->serializer = new Serializer(
            normalizers: [
                new BackedEnumNormalizer(),
                new ObjectNormalizer(propertyTypeExtractor: new ConstructorExtractor()),
            ]
        );

        $this->http = new RetryableHttpClient(
            client: HttpClient::create(
                defaultOptions: [
                    'auth_bearer' => $this->credential->token,
                    'headers' => [
                        'Accept' => 'application/json',
                    ],
                ]
            ),
            strategy: new GenericRetryStrategy(delayMs: 500),
            maxRetries: 3
        );
    }

    /**
     * Cette interface permet d’envoyer une requête de paiement à FlexPay
     *
     * @since flexpay v1
     * @throws NetworkException
     */
    public function mobile(MobileRequest $request): PaymentResponse
    {
        $request->setCredential($this->credential);

        try {
            /** @var PaymentResponse $response */
            $response = $this->getMappedData(
                type: PaymentResponse::class,
                data: $this->http->request('POST', $this->environment->getMobilePaymentUrl(), [
                    'json' => $request->getPayload(),
                ])->toArray()
            );

            return $response;
        } catch (\Throwable $e) {
            $this->createExceptionFromResponse($e);
        }
    }

    /**
     * Cette interface permet d’envoyer une requête de paiement à FlexPay
     * Ce paiement va se faire en deux étapes :
     * - Générer l’url de paiement
     * - Redirection vers la page de paiements
     *
     * @since flexpay v1.1
     * @throws NetworkException
     */
    public function card(CardRequest $request): CardResponse
    {
        $request->setCredential($this->credential);

        try {
            /** @var CardResponse $response */
            $response = $this->getMappedData(
                type: CardResponse::class,
                data: $this->http->request('POST', $this->environment->getCardPaymentUrl(), [
                    'json' => $request->getPayload(),
                ])->toArray()
            );

            return $response;
        } catch (\Throwable $e) {
            $this->createExceptionFromResponse($e);
        }
    }

    /**
     * @throws NetworkException
     */
    public function pay(Request $request): PaymentResponse|CardResponse
    {
        return match (true) {
            $request instanceof MobileRequest => $this->mobile($request),
            $request instanceof CardRequest => $this->card($request),
            default => throw new \RuntimeException('Unsupported request')
        };
    }

    /**
     * Cette interface permet de vérifier l’état d’une requête de paiement envoyée à FlexPay.
     *
     * @param string $orderNumber Le code de la transaction généré par FlexPay lors de la requête de paiement
     *
     * @since flexpay v1
     * @throws NetworkException quand une erreur
     */
    public function check(string $orderNumber): CheckResponse
    {
        try {
            /** @var CheckResponse $response */
            $response = $this->getMappedData(
                type: CheckResponse::class,
                data: $this->http
                    ->request('GET', $this->environment->getCheckStatusUrl($orderNumber))
                    ->toArray()
            );

            return $response;
        } catch (\Throwable $e) {
            $this->createExceptionFromResponse($e);
        }
    }

    /**
     * Cette interface permet à un marchand d’envoyer à partir de son compte de l’argent électronique vers un
     * numéro de téléphone qui a un compte mobile money.
     *
     * @since flexpay v1.1
     * @throws NetworkException
     */
    public function payout(PayoutRequest $request): PayoutResponse
    {
        $request->setCredential($this->credential);

        try {
            /** @var PayoutResponse $response */
            $response = $this->getMappedData(
                type: PayoutResponse::class,
                data: $this->http->request('POST', $this->environment->getPayoutUrl(), [
                    'json' => $request->getPayload(),
                ])->toArray()
            );

            return $response;
        } catch (\Throwable $e) {
            $this->createExceptionFromResponse($e);
        }
    }

    /**
     * Cette interface permet de vérifier l’état d’une requête de paiement envoyée à FlexPay
     */
    public function handleCallback(array $data): PaymentResponse
    {
        /** @var PaymentResponse $payment */
        $payment = $this->getMappedData(PaymentResponse::class, $data);

        return $payment;
    }

    /**
     * @psalm-param class-string<FlexpayResponse> $type
     */
    private function getMappedData(string $type, array $data): FlexpayResponse
    {
        /** @var FlexpayResponse $mapped */
        $mapped = $this->serializer->denormalize($data, $type);

        return $mapped;
    }

    /**
     * @throws NetworkException
     */
    private function createExceptionFromResponse(\Throwable $exception): never
    {
        if ($exception instanceof HttpExceptionInterface) {
            try {
                $response = $exception->getResponse();
                $body = $response->toArray(throw: false);

                throw NetworkException::create(
                    message: $body['message'] ?? '',
                    type: $body['error'],
                    status: $response->getStatusCode()
                );
            } catch (\Throwable $exception) {
                throw new NetworkException($exception->getMessage());
            }
        } else {
            throw new NetworkException($exception->getMessage());
        }
    }
}
