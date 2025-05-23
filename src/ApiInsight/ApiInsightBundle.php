<?php

namespace ApiInsight;

use ApiInsight\DependencyInjection\ApiInsightExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ApiInsightBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new ApiInsightExtension();
    }
} 