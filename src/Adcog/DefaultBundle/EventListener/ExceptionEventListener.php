<?php

namespace Adcog\DefaultBundle\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class ExceptionEventListener
 *
 * @author "Emmanuel BALLERY" <emmanuel.ballery@gmail.com>
 */
class ExceptionEventListener
{
    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $debug;

    /**
     * @param EngineInterface $templating Templating
     * @param LoggerInterface $logger     Logger
     * @param bool            $debug      Debug
     */
    public function __construct(EngineInterface $templating, LoggerInterface $logger, $debug)
    {
        $this->templating = $templating;
        $this->logger = $logger;
        $this->debug = $debug;
    }

    /**
     * On kernel exception
     *
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        if (false === $this->debug) {
            $exception = $event->getException();
            $code = $exception instanceof HttpException ? $exception->getStatusCode() : 500;

            // Create a new response
            $response = $this->templating->renderResponse('AdcogDefaultBundle:Default:exception.html.twig', [
                'code' => $code,
                'message' => $exception->getMessage(),
            ]);
            $response->setStatusCode(404);
            $event->setResponse($response);
        }
    }
}
