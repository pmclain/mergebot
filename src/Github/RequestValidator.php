<?php
declare(strict_types=1);

namespace App\Github;

use App\Exception\RequestValidationException;
use Symfony\Component\HttpFoundation\Request;

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
     * @throws RequestValidationException
     */
    public function validate(Request $request): bool
    {
        $signature = $request->headers->get(self::HEADER_SIGNATURE);
        if (!$signature) {
            throw new RequestValidationException('Request signature not found. Was hook secret configured?');
        }

        $expected = self::SIGNATURE_PREFIX . hash_hmac('sha1', $request->getContent(), $this->secretKey);

        if ($expected !== $signature) {
            throw new RequestValidationException('Verification of the request failed.');
        }

        return true;
    }
}
