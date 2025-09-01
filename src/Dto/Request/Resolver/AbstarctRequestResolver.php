<?php

namespace App\Dto\Request\Resolver;

use App\Dto\Request\RequestDtoInterface;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;


abstract class AbstarctRequestResolver implements ValueResolverInterface
{
    // Check if the resolver supports the given argument.
    protected function supports(ArgumentMetadata $argument, string $requestedDtoClass): bool
    {
        // Ensure the DTO implements the RequestDtoInterface
        if (!is_a($requestedDtoClass, RequestDtoInterface::class, true)) {
            throw new \InvalidArgumentException(sprintf(
                '%s must implement %s',
                $requestedDtoClass,
                RequestDtoInterface::class
            ));
        }

        // Check if the argument type matches the requested DTO class
        return is_a($argument->getType(), $requestedDtoClass, true);
    }

    /** 
     * Resolve the argument to an instance of the requested DTO.
     * 
     * @param Request $request The current HTTP request.
     * @param ArgumentMetadata $argument The metadata of the argument to resolve.
     * @return iterable<RequestDtoInterface> An iterable yielding the resolved DTO instance.
     */
    abstract public function resolve(Request $request, ArgumentMetadata $argument): iterable;
}
