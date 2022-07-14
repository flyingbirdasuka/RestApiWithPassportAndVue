<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Exception;
use App\Traits\ApiResponser;
use App\Exceptions\InvalidOrderException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;


class Handler extends ExceptionHandler
{
    use ApiResponser;
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => "Object not found.",
                    'code' => '404',
                ], 404);
            }
         });
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => "The specified method for the request is invalid.",
                    'code' => '405',
                ], 405);
            }
         });
        $this->renderable(function (ModelNotFoundException $e, $request) {
            $modelName = strtolower(class_basename($e->getModel()));
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => "Does not exists any {$modelName} with the specified identificator",
                    'code' => '404',
                ], 404);
            }
         });
        $this->renderable(function (AuthenticationException $e, $request) {
            if ($request->is('api/*')) {
                return $this->unauthenticated($request, $e);
            }
         });
        $this->renderable(function (AuthorizationException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => '403',
                ], 403);
            }
         });
        $this->renderable(function (QueryException $e, $request) {
            $errorCode = $e->errorInfo[1];
            if ($request->is('api/*')) {
                if($errorCode == 1451){
                    return response()->json([
                        'message' => 'Cannot remove this resource permanently. It is related with any other resource',
                        'code' => '409',
                    ], 409);
                }
            }
        });
        $this->renderable(function (HttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'code' => $e->getStatusCode(),
                ], $e->getStatusCode());
            }
        });
        
        if(!config('app.debug')){
            return $this->errorResponse('unexpected exception, please try later', 500);
        }
        
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
        if ($this->isFrontend($request)) {
            return redirect()->guest('login');
        }

        return $this->errorResponse('Unauthenticated.', 401);
    }
    
    private function isFrontend($request)
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
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
        $errors = $e->validator->errors()->getMessages();

        if($this->isFrontend($request)){
            return $request->ajax() ? response()->json($errors, 422) : redirect()
            ->back()
            ->withInput($request->input())
            ->withErrors($errors);
        }

        return $this->errorResponse($errors, 422);
    }

}
