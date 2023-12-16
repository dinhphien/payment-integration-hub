<?php

declare(strict_types=1);

namespace App\CommandHandlers\Handlers;

use App\CommandHandlers\Commands\CommandInterface;

interface CommandHandlerInterface
{
    public function execute(CommandInterface $command): mixed;
}
