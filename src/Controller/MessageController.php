<?php

declare(strict_types=1);

namespace App\Controller;


/**
 * delete unused use imports because they add noise to the code, 
 * reduce readability, may confuse developers into thinking a class or interface is used when it isn’t,
 *  and can trigger warnings from static analysis tools
 */

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use App\Dto\Request\Impl\MessageListRequestDto;
use App\Dto\Request\Impl\SendMessageRequestDto;
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

    /** Change the route method to post */
    #[Route('/messages/send', methods: ['POST'])]
    public function send(SendMessageRequestDto $messageDto): Response
    {

        /** 
        * TODO: currently, the repository receives the raw Request object.
        * Enhancement: create a typed DTO (e.g., MessageListRequestDto) and map query params to it.
        * Preferably to rename the method "by" to something more descriptive like "findByStatus".
        * Move the validation to dto and use use Symfony\Component\Validator\Constraints as Assert.
        * Use transformers/serializers to extract only the relevant data.
        * This keeps repositories focused on data access, services on business logic, and controllers thin and expressive. 
        * Move the dispatch logic to the service layer
        * to keep controllers thin and focused on handling HTTP requests/responses.
        * This also makes it easier to test business logic in isolation.*/
        $this->messageService->sendMessage($messageDto->text);

        /**  Using the abstract base controller’s ->json() method ensures cleaner, 
         * consistent, and safer JSON responses with proper headers and encoding handled automatically. */
        return $this->json(
            null,
            Response::HTTP_NO_CONTENT,
        );
    }
}
