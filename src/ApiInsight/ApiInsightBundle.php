<?php

namespace ApiInsight;

use ApiInsight\DependencyInjection\ApiInsightExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class ApiInsightBundle extends Bundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new ApiInsightExtension();
    }
}
