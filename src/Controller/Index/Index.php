<?php
declare(strict_types=1);

namespace App\Controller\Index;

use Symfony\Component\HttpFoundation\Response;

class Index
{
    public function execute(): Response
    {
        return new Response($this->getTemplate());
    }

    /**
     * Don't want to bother with template engine.
     * This will work for now :(
     */
    private function getTemplate(): string
    {
        // @codingStandardsIgnoreStart
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="theme-color" content="#000000">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link rel="stylesheet" href="/build/app.css">
    <title>Mergebot</title>
  </head>
  <body>
    <noscript>
      You need to enable JavaScript to run this app.
    </noscript>
    <div id="root"></div>
    <script type="text/javascript" src="/build/app.js"></script>
    <script type="text/javascript" src="/build/runtime.js"></script>
  </body>
</html>
HTML;
    // @codingStandardsIgnoreEnd
    }
}
