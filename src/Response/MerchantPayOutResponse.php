<?php

declare(strict_types=1);

namespace Devscast\Flexpay\Response;

use Symfony\Contracts\HttpClient\ResponseInterface;

final class MerchantPayOutResponse extends FlexpayResponse
{
    public function getResponse(ResponseInterface $response): array
    {
        try {
            $dataResponse = $response->toArray();

            if (isset($dataResponse['code']) && $dataResponse['code'] === '200') {
                return [
                    'status' => 'Transaction sent successfully.',
                    'message' => $dataResponse['message'] ?? 'Transaction succeeded',
                    'transaction_id' => $dataResponse['transactionId'] ?? null,
                ];
            }

            return [
                'status' => 'Transaction failed.',
                'message' => $dataResponse['message'] ?? 'Transaction failed',
                'code' => $dataResponse['code'] ?? 'Unknown error',
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'Error processing response.',
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }
}
