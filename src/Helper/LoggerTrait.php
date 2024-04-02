<?php

namespace App\Helper;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait LoggerTrait
{
    protected LoggerInterface $logger;

    #[Required]
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param string|\Stringable $message
     * @param array<string, mixed> $context
     */
    private function logError(string|\Stringable $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    /**
     * @param string|\Stringable $message
     * @param array<string, mixed> $context
     */
    private function logWarning(string|\Stringable $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    /**
     * @param string|\Stringable $message
     * @param array<string, mixed> $context
     */
    private function logNotice(string|\Stringable $message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }

    /**
     * @param string|\Stringable $message
     * @param array<string, mixed> $context
     */
    private function logInfo(string|\Stringable $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    /**
     * @param string|\Stringable $message
     * @param array<string, mixed> $context
     */
    private function logDebug(string|\Stringable $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }
}
