<?php

namespace App\Controller;

use App\Domain\Message\Dto\ScheduleSendingMessage;
use App\Domain\Message\MessageManager;
use Doctrine\ORM\EntityNotFoundException;
use JMS\Serializer\Exception\RuntimeException;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class MessageController extends AbstractController
{
    private $messageManager;
    private $serializer;

    public function __construct(MessageManager $messageManager, SerializerInterface $serializer)
    {
        $this->messageManager = $messageManager;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/message", name="createMessage", methods={"POST"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function createMessage(Request $request): Response
    {
        try {
            /** @var ScheduleSendingMessage $data */
            $message = $this->serializer->deserialize(
                $request->getContent(),
                ScheduleSendingMessage::class,
                'json'
            );

            $event = $this->messageManager->scheduleSending($message);

            $serializedData = $this->serializer->serialize(
                $event->getMessage(),
                'json',
                SerializationContext::create()->setSerializeNull(true)
            );

            return new JsonResponse(json_decode($serializedData, true), Response::HTTP_CREATED);
        } catch (RuntimeException $e) {
            return new JsonResponse(['error' => true], Response::HTTP_BAD_REQUEST);
        } catch (Throwable $e) {
            return new JsonResponse(['error' => true], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/message", name="listMessages", methods={"GET"})
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function listMessages(Request $request): Response
    {
        try {
            $offset = $request->get('offset');
            $limit = $request->get('limit');

            $messages = $this->messageManager->getMessages($limit, $offset);

            $serializedData = $this->serializer->serialize(
                $messages,
                'json',
                SerializationContext::create()->setSerializeNull(true)
            );

            return new JsonResponse(json_decode($serializedData, true), Response::HTTP_OK);

        } catch (Throwable $e) {
            return new JsonResponse(['error' => true], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @Route("/message/{id}", name="cancelMessage", methods={"DELETE"})
     * @param int $id
     * @return Response
     */
    public function deleteMessage(int $id): Response
    {
        try {
            $message = $this->messageManager->getMessage($id);

            if (null === $message) {
                throw new EntityNotFoundException();
            }

            $event = $this->messageManager->cancelSending($message);

            $serializedData = $this->serializer->serialize(
                $event->getMessage(),
                'json',
                SerializationContext::create()->setSerializeNull(true)
            );

            return new JsonResponse(json_decode($serializedData, true), Response::HTTP_OK);

        } catch (EntityNotFoundException $e) {
            return new JsonResponse(['error' => true], Response::HTTP_NOT_FOUND);
        } catch (Throwable $e) {
            return new JsonResponse(['error' => true], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
