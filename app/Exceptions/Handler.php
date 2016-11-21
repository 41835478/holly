<?php

namespace App\Exceptions;

use Exception;
use Holly\Http\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
        ActionFailureException::class,
        InvalidInputException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        if ($this->shouldntReport($exception)) {
            return;
        }

        $requestInfo = $this->getRequestInfo($this->container['request']);

        $this->container['log']->error($exception, $requestInfo);

        $this->notifyException($exception, $requestInfo);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof MaintenanceModeException) {
            return $this->createApiResponse('服务器维护中，请稍后重试。', 503);
        }

        if ($exception instanceof ActionFailureException ||
            $exception instanceof InvalidInputException
        ) {
            return $this->renderActionFailure($request, $exception);
        }

        return parent::render($request, $exception);
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return $this->createApiResponse('认证失败，请先登录。', 401);
        }

        return redirect()->guest('login');
    }

    /**
     * Get the request info.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function getRequestInfo($request)
    {
        $info = [
            'IP' => $request->ips(),
            'UserAgent' => $request->server('HTTP_USER_AGENT'),
            'URL' => $request->fullUrl(),
        ];

        if (app()->runningInConsole()) {
            $info['Command'] = implode(' ', $request->server('argv', []));
        }

        return $info;
    }

    /**
     * Notify the exception.
     *
     * @param  \Exception  $exception
     * @param  array  $requestInfo
     */
    protected function notifyException(Exception $exception, $requestInfo = null)
    {
        if (app()->environment('production')) {
            // dispatch(
            //     (new SendBearyChat)
            //     ->client('server')
            //     ->text('New Exception!')
            //     ->notification('New Exception: '.get_class($exception))
            //     ->markdown(false)
            //     ->add(str_limit($exception, 1300), get_class($exception), null, '#a0a0a0')
            //     ->add(str_limit(string_value($requestInfo), 1300), 'Request Info', null, '#e67f0a')
            // );
        }
    }

    /**
     * Create an API response.
     *
     * @param  mixed  $message
     * @param  int  $code
     * @return \Holly\Http\ApiResponse
     */
    protected function createApiResponse($message = null, $code = null)
    {
        $response = new ApiResponse($message, $code);

        if (($successCode = $response::successCode()) === $response->getCode()) {
            $response->setCode(-1 * $successCode);
        }

        if (empty($response->getMessage())) {
            $response->setMessage('发生错误，操作失败！');
        }

        return $response;
    }

    /**
     * Create an API response for the given exception.
     *
     * @param  \Exception  $e
     * @return \Holly\Http\ApiResponse
     */
    protected function convertExceptionToApiResponse(Exception $e)
    {
        return $this->createApiResponse($e->getMessage(), $e->getCode());
    }

    /**
     * Render the given exception of action failure.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderActionFailure($request, Exception $exception)
    {
        if ($request->expectsJson()) {
            return $this->convertExceptionToApiResponse($exception);
        }

        return redirect()->back()->withInput($request->input())->with('alert.error', $exception->getMessage());
    }

    /**
     * Render the given HttpException.
     *
     * @param  \Symfony\Component\HttpKernel\Exception\HttpException  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function renderHttpException(HttpException $e)
    {
        if ($this->container['request']->expectsJson()) {
            $status = $e->getStatusCode();
            $message = $e->getMessage();

            if (401 === $status) {
                $message = '认证失败，请先登录';
            } elseif (403 === $status) {
                $message = '无权操作，拒绝访问';
            } elseif (404 === $status) {
                $message = '404 Not Found';
            } else {
                $message = "非法操作 [{$status}]";
            }

            return $this->createApiResponse($message, $status);
        }

        return parent::renderHttpException($e);
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        if ($request->expectsJson()) {
            return $this->createApiResponse(implode("\n", array_flatten($e->validator->errors()->getMessages())), 422);
        }

        return parent::convertValidationExceptionToResponse($e, $request);
    }

    /**
     * Create a Symfony response for the given exception.
     *
     * @param  \Exception  $e
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertExceptionToResponse(Exception $e)
    {
        if (! $this->container['config']['app.debug'] && $this->container['request']->expectsJson()) {
            return $this->createApiResponse(['__error' => $e->getMessage()], $e->getCode());
        }

        return parent::convertExceptionToResponse($e);
    }
}
