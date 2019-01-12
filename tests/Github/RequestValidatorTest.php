<?php

namespace App\Tests\Github;

use App\Github\RequestValidator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\HeaderBag;

class RequestValidatorTest extends TestCase
{
    const TEST_KEY = 'testing123';

    /**
     * @var Request|MockObject
     */
    private $requestMock;

    /**
     * @var HeaderBag|MockObject
     */
    private $headersMock;

    /**
     * @var RequestValidator
     */
    private $validator;

    protected function setUp()
    {
        $this->requestMock = $this->createMock(Request::class);

        $this->headersMock = $this->createMock(HeaderBag::class);

        $this->requestMock->headers = $this->headersMock;

        $this->validator = new RequestValidator(self::TEST_KEY);
    }

    public function testValidate()
    {
        $content = json_encode(['test' => 'data']);
        $this->requestMock->method('getContent')->willReturn($content);

        $this->headersMock->method('get')
            ->with(RequestValidator::HEADER_SIGNATURE)
            ->willReturn(RequestValidator::SIGNATURE_PREFIX . hash_hmac('sha1', $content, self::TEST_KEY));

        $this->assertTrue($this->validator->validate($this->requestMock));
    }

    /**
     * @expectedException \App\Exception\RequestValidationException
     */
    public function testValidateNoMatch()
    {
        $content = json_encode(['test' => 'data']);
        $this->requestMock->method('getContent')->willReturn($content . 's');

        $this->headersMock->method('get')
            ->with(RequestValidator::HEADER_SIGNATURE)
            ->willReturn(RequestValidator::SIGNATURE_PREFIX . hash_hmac('sha1', $content, self::TEST_KEY));

        $this->assertTrue($this->validator->validate($this->requestMock));
    }

    /**
     * @expectedException \App\Exception\RequestValidationException
     */
    public function testValidateNoHeader()
    {
        $this->validator->validate($this->requestMock);
    }
}
