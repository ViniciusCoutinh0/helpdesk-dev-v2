<?php

use Pecee\SimpleRouter\SimpleRouter as Router;
use Pecee\Http\Url;
use Pecee\Http\Response;
use Pecee\Http\Request;
use App\Common\Session;

/**
 * Get url for a route by using either name/alias, class or method name.
 *
 * The name parameter supports the following values:
 * - Route name
 * - Controller/resource name (with or without method)
 * - Controller class name
 *
 * When searching for controller/resource by name, you can use this syntax "route.name@method".
 * You can also use the same syntax when searching for a specific controller-class "MyController@home".
 * If no arguments is specified, it will return the url for the current loaded route.
 *
 * @param string|null $name
 * @param string|array|null $parameters
 * @param array|null $getParams
 * @return \Pecee\Http\Url
 * @throws \InvalidArgumentException
 */
function url(?string $name = null, $parameters = null, ?array $getParams = null): Url
{
    return Router::getUrl($name, $parameters, $getParams);
}

/**
 * @return \Pecee\Http\Response
 */
function response(): Response
{
    return Router::response();
}

/**
 * @return \Pecee\Http\Request
 */
function request(): Request
{
    return Router::request();
}

/**
 * Get input class
 * @param string|null $index Parameter index name
 * @param string|mixed|null $defaultValue Default return value
 * @param array ...$methods Default methods
 * @return \Pecee\Http\Input\InputHandler|array|string|null
 */
function input($index = null, $defaultValue = null, ...$methods)
{
    if ($index !== null) {
        return request()->getInputHandler()->value($index, $defaultValue, ...$methods);
    }

    return request()->getInputHandler();
}

/**
 * @param string $url
 * @param int|null $code
 */
function redirect(string $url, ?int $code = null): void
{
    if ($code !== null) {
        response()->httpCode($code);
    }

    response()->redirect($url);
}

/**
 * Get current csrf-token
 * @return string|null
 */
function csrf_token(): ?string
{
    $baseVerifier = Router::router()->getCsrfVerifier();
    if ($baseVerifier !== null) {
        return $baseVerifier->getTokenProvider()->getToken();
    }

    return null;
}

/**
 * @return null|string
*/
function old(string $key): ?string
{
    if (input()->exists($key)) {
        return input()->post($key)->getValue();
    }

    return null;
}

/**
 * @return string
 */
function defaultUrl(): string
{
    if (\request()->getHost() == 'localhost') {
        return $_ENV['CONFIG_APP_DEV_URL'];
    }
    return $_ENV['CONFIG_APP_PRO_URL'];
}

/**
 * @return string
 */
function asset(string $path = null): string
{
    if ($path) {
        return defaultUrl() . '/' . $path;
    }
    return defaultUrl();
}

/**
 * @return string
*/
function pathOs(string $path, string $default = '/'): string
{
    return str_replace($default, DIRECTORY_SEPARATOR, $path);
}

/**
 * @return Session
*/
function Session(): Session
{
    return new Session();
}
/**
 * @return string
*/
function env(string $key): string 
{
    return $_ENV[$key];
}

/**
 * @return string
*/
function clearHtml(string $str): string
{
    return htmlentities(strip_tags($str), ENT_QUOTES, 'UTF-8');
}

/**
 * @link https://gist.github.com/quantizer/5744907
 * @return string
*/
function clearEmoji(string $str): string
{
    $cleanText = "";

    $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $cleanText = preg_replace($regexEmoticons, '', $str);

    $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $cleanText = preg_replace($regexSymbols, '', $str);

    $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
    $cleanText = preg_replace($regexTransport, '', $str);

    return $cleanText;
}
