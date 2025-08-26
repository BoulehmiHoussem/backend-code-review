<?php

declare(strict_types=1);

namespace App\Controller;


/**
 * delete unused use imports because they add noise to the code, 
 * reduce readability, may confuse developers into thinking a class or interface is used when it isn’t,
 *  and can trigger warnings from static analysis tools
 */

use App\Message\SendMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Dto\Request\MessageListRequestDto;
use App\Service\MessageService;

/**
 * @see MessageControllerTest
 * TODO: review both methods and also the `openapi.yaml` specification
 *       Add Comments for your Code-Review, so that the developer can understand why changes are needed.
 */
class MessageController extends AbstractController
{
    /**
     * Constructor to initialize the MessageService and MessageBusInterface.
     * @param MessageService $messageService The service handling message operations.
     * @param MessageBusInterface $bus The message bus for dispatching messages.
     */
    public function __construct(
        private MessageService $messageService,
        private MessageBusInterface $bus,
    ) {}

    /**
     * TODO: cover this method with tests, and refactor the code (including other files that need to be refactored)
     */
    #[Route('/messages')]

    /** We need to create a resolver to automatically map request parameters to the MessageListRequestDto
     * so that controllers stay clean and focused, request validation and type safety are centralized, 
     * and the mapping from raw request data (query/body) to a well-typed DTO happens consistently 
     * and automatically without repetitive boilerplate code.
     * @param MessageListRequestDto $params The parameters for listing messages, automatically resolved from the request.
     * @return Response The JSON response containing the list of messages.
     */

    public function list(MessageListRequestDto $params): Response
    {
        /** Passing the raw Request object directly to 
         * a repository couples persistence logic with HTTP concerns, 
         * which breaks separation of concerns; by moving the logic to the service layer, 
         * renaming the method to something descriptive like findByStatus, 
         * and using transformers/serializers to extract only the relevant data, 
         * we ensure that repositories stay focused on data access, services handle business logic, 
         * and controllers remain thin and expressive.
         */
        $messages = $this->messageService->listMessages($params);

        /**  Using the abstract base controller’s ->json() method ensures cleaner, 
         * consistent, and safer JSON responses with proper headers and encoding handled automatically. */
        return $this->json(
            $messages,
            Response::HTTP_OK,
            ['Content-Type' => 'application/json'],
            ['json_encode_options' => JSON_THROW_ON_ERROR]
        );
    }

    #[Route('/messages/send', methods: ['GET'])]
    public function send(Request $request): Response
    {
        $message = $request->query->get('text');

        // TODO: currently, the repository receives the raw Request object.
        // Enhancement: create a typed DTO (e.g., MessageListRequestDto) and map query params to it.
        // Preferably to rename the method "by" to something more descriptive like "findByStatus". 

        if (!$message) {
            return new Response('Text is required', Response::HTTP_BAD_REQUEST);
        }

        $this->bus->dispatch(new SendMessage($message));

        return new Response('Successfully sent', Response::HTTP_NO_CONTENT);
    }
}
