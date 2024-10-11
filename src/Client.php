<?php

declare(strict_types=1);

namespace Devscast\Flexpay;

use Devscast\Flexpay\Exception\NetworkException;
use Devscast\Flexpay\Request\MobileRequest;
use Devscast\Flexpay\Request\Request;
use Devscast\Flexpay\Request\VposRequest;
use Devscast\Flexpay\Response\CheckResponse;
use Devscast\Flexpay\Response\FlexpayResponse;
use Devscast\Flexpay\Response\PaymentResponse;
use Devscast\Flexpay\Response\VposResponse;
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
     * Permet d'envoyer une directement intention de paiement sur le mobile money du client
     *
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
     * Créer une URL unique de paiement via le gateway de Flexpay
     * Cela permet d'utiliser différente méthode de paiement
     * y compris une carte bancaire (VISA, MASTERCARD, etc.)
     *
     * @throws NetworkException
     */
    public function vpos(VposRequest $request): VposResponse
    {
        $request->setCredential($this->credential);

        try {
            /** @var VposResponse $response */
            $response = $this->getMappedData(
                type: VposResponse::class,
                data: $this->http->request('POST', $this->environment->getVposAskUrl(), [
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
    public function pay(Request $request): PaymentResponse|VposResponse
    {
        return match (true) {
            $request instanceof MobileRequest => $this->mobile($request),
            $request instanceof VposRequest => $this->vpos($request),
            default => throw new \RuntimeException('Unsupported request')
        };
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
     * Cette interface permet d'obtenir une réponse de paiement provenant de FlexPay
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
