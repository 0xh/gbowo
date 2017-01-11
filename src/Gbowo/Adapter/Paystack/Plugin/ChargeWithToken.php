<?php

namespace Gbowo\Adapter\Paystack\Plugin;

use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use Gbowo\Plugin\AbstractChargeWithToken;
use Gbowo\Adapter\Paystack\Exception\TransactionVerficationFailedException;

class ChargeWithToken extends AbstractChargeWithToken
{

    /**
     * The relative link for charging users
     * @var string
     */
    const TOKEN_CHARGE_RELATIVE_LINK = "/transaction/charge_authorization";

    const SUCCESS_MESSAGE = "Successful";
    /**
     * @var string
     */
    protected $baseUrl;

    public function __construct(string $baseUrl)
    {
        $this->baseUrl = $baseUrl;
    }

    public function handle(...$args)
    {
        $response = $this->chargeByToken($args);

        $res = json_decode($response->getBody(), true);

        if (strcmp($res['data']['gateway_response'], self::SUCCESS_MESSAGE) !== 0) {
            throw new TransactionVerficationFailedException(
                "The transaction was not successful"
            );
        }

        return $res['data'];
    }

    public function chargeByToken($data)
    {
        return $this->adapter->getHttpClient()
            ->post($this->baseUrl . self::TOKEN_CHARGE_RELATIVE_LINK, [
                'body' => json_encode($data)
            ]);
    }
}
