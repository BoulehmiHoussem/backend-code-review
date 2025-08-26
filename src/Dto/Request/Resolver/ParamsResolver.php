<?php
declare(strict_types=1);
namespace App\Dto\Request\Resolver;

use App\Dto\Request\MessageListRequestDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

class ParamsResolver implements ValueResolverInterface
{
    /**
     * Checks if the resolver supports the given argument.
     * @param Request $request The current HTTP request.
     * @param ArgumentMetadata $argument The metadata of the argument to resolve.
     * @return bool True if the argument type is MessageListRequestDto, false otherwise.
     */
    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        // Check if the argument type matches MessageListRequestDto
        return $argument->getType() === MessageListRequestDto::class;
    }

    /**
     * Resolves the argument value from the request.
     * @param Request $request The current HTTP request.
     * @param ArgumentMetadata $argument The metadata of the argument to resolve.
     * @return iterable<MessageListRequestDto>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        // Extract the 'status' query parameter from the request
        $status = $request->query->get('status');

        // Yield a new instance of MessageListRequestDto with the extracted status
        yield new MessageListRequestDto(
            status: $status !== null ? (string) $status : null
        );
    }
}
