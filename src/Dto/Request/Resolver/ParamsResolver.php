<?php

declare(strict_types=1);

namespace App\Dto\Request\Resolver;

use App\Dto\Request\Impl\MessageListRequestDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ParamsResolver extends AbstarctRequestResolver
{
    /** Resolve the argument value from the request.
     *
     * @param Request $request The current HTTP request.
     * @param ArgumentMetadata $argument The metadata of the argument to resolve.
     * @return iterable<MessageListRequestDto> An iterable yielding the resolved DTO instance.
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {

        if (!$this->supports($argument, MessageListRequestDto::class)) return;

        // Extract the 'status' query parameter from the request
        $status = $request->query->get('status');

        // Yield a new instance of MessageListRequestDto with the extracted status
        yield new MessageListRequestDto(
            status: $status !== null ? (string) $status : null
        );
    }
}
