<?php

namespace Carsguide\Auth\Middlewares;

use Auth0\SDK\Exception\CoreException;
use Auth0\SDK\Exception\InvalidTokenException;
use Auth0\SDK\Helpers\JWKFetcher;
use Auth0\SDK\Helpers\Tokens\AsymmetricVerifier;
use Auth0\SDK\Helpers\Tokens\IdTokenVerifier;
use Carsguide\Auth\Handlers\CacheHandler;
use Closure;
use Illuminate\Http\JsonResponse;

class AddJwtNamespaceFieldsMiddleware
{
    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure $next
     * @param  string $scope
     * @return mixed
     */
    public function handle($request, Closure $next, $fields)
    {
        $this->request = $request;

        //verify and decode token
        try {
            $this->verifyAndDecodeToken($this->request->bearerToken());
        } catch (CoreException | InvalidTokenException $e) {
            //Log::info('Invalid token');
            //return $this->json('Invalid token', 401);
        }

        $this->explodeFields($fields);

        $this->addFieldsToRequest();

        return $next($this->request);
    }

    /**
     * verify and decode the token
     *
     * @param string $token
     * @return void
     */
    public function verifyAndDecodeToken($token)
    {
        $jwksFetcher = new JWKFetcher(new CacheHandler());
        $jwks        = $jwksFetcher->getKeys();
        $sigVerifier = new AsymmetricVerifier($jwks);

        $idTokenVerifier = new IdTokenVerifier(env('AUTH0_DOMAIN', false), env('AUTH0_AUDIENCE', false), $sigVerifier);
        $this->decodedToken = $idTokenVerifier->verify($token);
    }

    /**
     * Explode fields
     *
     * @param $fields
     * @return void
     */
    public function explodeFields($fields)
    {
        $this->fields = explode(':', $fields);
    }

    /**
     * Explode fields
     *
     * @return void
     */
    public function addFieldsToRequest()
    {
        foreach ($this->fields as $field) {
            $namespaceField = $this->getJwtNamespace() . $field;

            $value = !empty($this->decodedToken->$namespaceField) ? $this->decodedToken->$namespaceField : null;

            $input = ['jwt' => ['namespace' => [$field => $value]]];

            $this->request->merge($input);
        }
    }

    /**
     * Get JWT namespace
     *
     * @return string
     */
    protected function getJwtNamespace()
    {
        return env('AUTH0_JWT_NAMESPACE', 'https://platform.autotrader.com.au/');
    }

    /**
     * Return a new JSON response from the application.
     *
     * @param  string|array  $data
     * @param  int    $status
     * @param  array  $headers
     * @param  int    $options
     * @return \Illuminate\Http\JsonResponse;
     */
    protected function json($data = [], $status = 200, array $headers = [], $options = 0)
    {
        return new JsonResponse($data, $status, $headers, $options);
    }
}
