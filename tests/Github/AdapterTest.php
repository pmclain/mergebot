<?php

namespace App\Tests\Github;

use App\Github\Adapter;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AdapterTest extends TestCase
{
    /**
     * @var Client|MockObject
     */
    private $clientMock;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Response
     */
    private $response;

    /**
     * @var array
     */
    private $responseBody = [
        'this' => 'is',
        'the' => 'response',
    ];

    /**
     * @var Adapter
     */
    private $adapter;

    protected function setUp()
    {
        $this->clientMock = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->setMethods(['get', 'post', 'put'])
            ->getMock();
        $this->request = new Request('get', 'url');
        $this->response = new Response('403', [], 'you shall not pass');
        $this->adapter = new Adapter('user', 'pass', $this->clientMock);
    }

    public function testGet()
    {
        $response = new Response(200, [], json_encode($this->responseBody));
        $this->clientMock->method('get')->willReturn($response);

        $this->assertEquals($this->responseBody, $this->adapter->get('https://nope.nopers'));
    }

    /**
     * @expectedException \App\Exception\HttpResponseException
     */
    public function testGetWithException()
    {
        $this->clientMock->method('get')->willThrowException(new ClientException('', $this->request, $this->response));

        $this->adapter->get('https://nope.nopers');
    }

    public function testGetRaw()
    {
        $response = new Response(200, [], json_encode($this->responseBody));
        $this->clientMock->method('get')->willReturn($response);

        $this->assertEquals(json_encode($this->responseBody), $this->adapter->getRaw('https://nope.nopers'));
    }

    /**
     * @expectedException \App\Exception\HttpResponseException
     */
    public function testGetRawWithException()
    {
        $this->clientMock->method('get')->willThrowException(new ClientException('', $this->request, $this->response));

        $this->adapter->getRaw('https://nope.nopers');
    }

    public function testPost()
    {
        $response = new Response(200, [], json_encode($this->responseBody));
        $this->clientMock->method('post')->willReturn($response);

        $this->assertEquals($this->responseBody, $this->adapter->post('https://nope.nopers'), []);
    }

    /**
     * @expectedException \App\Exception\HttpResponseException
     */
    public function testPostWithException()
    {
        $this->clientMock->method('post')->willThrowException(new ClientException('', $this->request, $this->response));

        $this->adapter->post('https://nope.nopers', []);
    }

    public function testPut()
    {
        $response = new Response(200, [], json_encode($this->responseBody));
        $this->clientMock->method('put')->willReturn($response);

        $this->assertEquals($this->responseBody, $this->adapter->put('https://nope.nopers'), []);
    }

    /**
     * @expectedException \App\Exception\HttpResponseException
     */
    public function testPutWithException()
    {
        $this->clientMock->method('put')->willThrowException(new ClientException('', $this->request, $this->response));

        $this->adapter->put('https://nope.nopers', ['data' => 'here']);
    }
}
