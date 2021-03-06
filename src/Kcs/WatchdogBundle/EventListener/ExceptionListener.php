<?php

namespace Kcs\WatchdogBundle\EventListener;

use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\SecurityContext;

use Kcs\WatchdogBundle\Storage\StorageInterface;
use Kcs\WatchdogBundle\Debug\ExceptionHandler;

/**
 * ExceptionListener is registered as event listener service on
 * kernel.exception event.
 *
 * When an unhandled exception is thrown the HttpKernel catches it
 * and fire the kernel.exception event to the listeners.
 * The onKernelException method passes the exception from the event
 * object to the exception handler.
 *
 * @author Alessandro Chitolina <alekitto@gmail.com>
 */
class ExceptionListener
{
    const TYPE_DEPRECATION = -100;

    /**
     * Security Context
     * @var SecurityContext
     */
    private $context = null;

    /**
     * Entity Storage Interface
     * @var StorageInterface
     */
    private $storage = null;

    /**
     * Exception Handler
     * @var ExceptionHandler
     */
    private $handler = null;

    /**
     * Exceptions not to be logged
     * @var string[]
     */
    private $allowedExceptions = array();

    public function __construct(SecurityContext $context, StorageInterface $storage,
            $debug, array $allowedExceptions) {
        $this->context = $context;
        $this->storage = $storage;
        $this->allowedExceptions = $allowedExceptions;

        // Initialize the exception handler
        $this->handler = new ExceptionHandler($debug);
        set_exception_handler(array($this, 'exceptionHandler'));
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $response = $this->handleException($event->getException());

        if($response !== null) {
            $event->setResponse($response);
        }
    }

    public function onConsoleException(ConsoleExceptionEvent $event)
    {
        $this->handleException($event->getException());
    }

    public function exceptionHandler(\Exception $exception)
    {
        $this->handleException($exception);
    }

    public function handleException(\Exception $exception)
    {
        $exception_class = get_class($exception);
        if(in_array($exception_class, $this->allowedExceptions))
            return;

        return $this->handler->handle($exception, $this->storage, $this->context->getToken());
    }
}

