<?php

namespace App\Github;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class RequestValidator
{
    const HEADER_SIGNATURE = 'X-Hub-Signature';
    const SIGNATURE_PREFIX = 'sha1=';

    /**
     * @var string
     */
    private $secretKey;

    public function __construct(
        string $secretKey
    ) {
        $this->secretKey = $secretKey;
    }

    /**
     * @param Request $request
     * @return bool
     * @throws UnauthorizedHttpException
     */
    public function validate(Request $request): bool
    {
        $signature = $request->headers->get(self::HEADER_SIGNATURE);
        if (!$signature) {
            throw new UnauthorizedHttpException('Request signature not found. Was hook secret configured?');
        }

        $expected = self::SIGNATURE_PREFIX . hash_hmac('sha1', $request->getContent(), $this->secretKey);

        if ($expected !== $signature) {
            throw new UnauthorizedHttpException('Verification of the request failed.');
        }

        return true;
    }
}
