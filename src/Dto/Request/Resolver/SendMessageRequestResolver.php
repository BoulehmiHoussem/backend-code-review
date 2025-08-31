<?php
declare(strict_types=1);
namespace App\Dto\Request\Resolver;

use App\Dto\Request\Impl\SendMessageRequestDto;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use App\Dto\Request\Resolver\AbstarctRequestResolver;

class SendMessageRequestResolver extends AbstarctRequestResolver
{
    /** Resolve the argument value from the request.
     *
     * @param Request $request The current HTTP request.
     * @param ArgumentMetadata $argument The metadata of the argument to resolve.
     * @return iterable<SendMessageRequestDto> An iterable yielding the resolved DTO instance.
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (!$this->supports($argument, SendMessageRequestDto::class)) return;
        // Extract the 'message' from the payload of the request
        assert(is_array($data = json_decode($request->getContent(), true)) || $data === null);
        $text = $data['text'] ?? null;

        // Yield a new instance of SendMessageRequestDto with the extracted text
        yield new SendMessageRequestDto(
            text: $text
        );
    }
}
