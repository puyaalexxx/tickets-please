<?php
declare(strict_types=1);


namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends \Illuminate\Foundation\Exceptions\Handler
{
    public function render($request, Throwable $e): Response
    {
        if ($request->is('api/*')) {

            $exceptionType = get_class($e);
            $className = $exceptionType;
            $source = 'Line: ' . $e->getLine() . ' in ' . $e->getFile();

            return match (true) {

                //custom exceptions
                $e instanceof TicketNotFoundException, $e instanceof UserNotFoundException => response()->json([
                    'success' => false,
                    'status' => Response::HTTP_NOT_FOUND,
                    'type' => class_basename($e),
                    'message' => $e->getMessage(),
                    'source' => $source
                ], Response::HTTP_NOT_FOUND),

                $e instanceof NotAuthorizedToEditTicketException, $e instanceof NotAuthorizedToEditUserException => response()->json([
                    'success' => false,
                    'status' => Response::HTTP_UNAUTHORIZED,
                    'type' => class_basename($e),
                    'message' => 'Authorization Issue.',
                    'details' => $e->getMessage(),
                    'source' => $source
                ], Response::HTTP_UNAUTHORIZED),

                //standard exceptions
                $e instanceof AuthenticationException => response()->json([
                    'success' => false,
                    'status' => Response::HTTP_UNAUTHORIZED,
                    'type' => $className,
                    'message' => 'Authentication Request.',
                    'details' => $e->getMessage(),
                    'source' => $source
                ], Response::HTTP_UNAUTHORIZED),

                $e instanceof AuthorizationException => response()->json([
                    'success' => false,
                    'status' => Response::HTTP_FORBIDDEN,
                    'type' => $className,
                    'message' => 'Unauthorized Request.',
                    'details' => $e->getMessage(),
                    'source' => $source
                ], Response::HTTP_FORBIDDEN),

                $e instanceof ValidationException => response()->json([
                    'success' => false,
                    'status' => Response::HTTP_UNPROCESSABLE_ENTITY,
                    'type' => $className,
                    'message' => 'Validation failed.',
                    'errors' => $e->errors(),
                    'source' => $source
                ], Response::HTTP_UNPROCESSABLE_ENTITY),

                $e instanceof NotFoundHttpException => response()->json([
                    'success' => false,
                    'status' => Response::HTTP_NOT_FOUND,
                    'type' => $className,
                    'message' => 'Resource not found.',
                    'details' => $e->getMessage(),
                    'source' => $source
                ], Response::HTTP_NOT_FOUND),

                $e instanceof HttpException => response()->json([
                    'success' => false,
                    'status' => $e->getStatusCode(),
                    'type' => $className,
                    'message' => $e->getMessage(),
                    'source' => $source
                ], $e->getStatusCode()),

                $e instanceof ModelNotFoundException => response()->json([
                    'success' => false,
                    'status' => Response::HTTP_NOT_FOUND,
                    'type' => $className,
                    'message' => 'Model not found.',
                    'details' => $e->getMessage(),
                    'source' => $source
                ], Response::HTTP_NOT_FOUND),

                default => response()->json([
                    'success' => false,
                    'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
                    'type' => $className,
                    'message' => 'An unexpected error occurred.',
                    'details' => $e->getMessage(),
                    'source' => $source
                ], Response::HTTP_INTERNAL_SERVER_ERROR),
            };
        }

        return parent::render($request, $e);
    }
}
