<?php

declare(strict_types=1);

namespace Devscast\Flexpay;

use Devscast\Flexpay\Data\Method;
use Devscast\Flexpay\Exception\NetworkException;
use Devscast\Flexpay\Response\CheckResponse;
use Devscast\Flexpay\Response\FlexpayResponse;
use Devscast\Flexpay\Response\PaymentResponse;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\Retry\GenericRetryStrategy;
use Symfony\Component\HttpClient\RetryableHttpClient;
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
final readonly class Client
{
    private HttpClientInterface $http;

    private Serializer $serializer;

    public function __construct(
        public Credential $credential,
        public Environment $environment = Environment::SANDBOX
    ) {
        $this->serializer = new Serializer(normalizers: [
            new BackedEnumNormalizer(),
            new ObjectNormalizer(),
        ]);

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
     * Cette interface permet d’envoyer une requête de redirection de paiement à FlexPay
     *
     * @throws NetworkException quand une erreur réseau survient
     * @throws \InvalidArgumentException quand le numéro de téléphone n'est pas fourni pour un paiement mobile
     */
    public function pay(PaymentEntry $entry, Method $method = Method::MOBILE): PaymentResponse
    {
        /**
         * Définit ici, pour éviter de préciser manuellement le marchant à chaque fois
         */
        $entry->setMerchant($this->credential->merchant);

        if ($method === Method::MOBILE && $entry->phone === null) {
            throw new \InvalidArgumentException('phone number should be provided for mobile payment');
        }

        try {
            /** @var PaymentResponse $response */
            $response = $this->getMappedData(
                type: PaymentResponse::class,
                data: $this->http->request(
                    method: 'POST',
                    url: sprintf('%s/paymentService', $this->environment->getBaseUrl()),
                    options: [
                        'json' => $this->serializer->normalize($entry),
                    ]
                )->toArray()
            );

            return $response;
        } catch (\Throwable $e) {
            $this->createExceptionFromResponse($e);
        }
    }

    /**
     * Cette interface permet d'obtenir une réponse de paiement provenant de FlexPay
     */
    public function handleCallback(array $data): PaymentResponse
    {
        /** @var PaymentResponse $payment */
        $payment = $this->getMappedData(PaymentResponse::class, $data);

        return $payment;
    }

    /**
     * Cette interface permet de vérifier l’état d’une requête de paiement envoyée à FlexPay.
     *
     * @param string $orderNumber Le code de la transaction généré par FlexPay lors de la requête de paiement
     *
     * @throws NetworkException quand une erreur
     */
    public function check(string $orderNumber): CheckResponse
    {
        try {
            /** @var CheckResponse $response */
            $response = $this->getMappedData(
                type: CheckResponse::class,
                data: $this->http->request(
                    method: 'GET',
                    url: sprintf('%s/check/%s', $this->environment->getBaseUrl(), $orderNumber)
                )->toArray()
            );

            return $response;
        } catch (\Throwable $e) {
            $this->createExceptionFromResponse($e);
        }
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
