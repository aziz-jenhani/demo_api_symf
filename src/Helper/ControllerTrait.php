<?php

namespace App\Helper;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Service\Attribute\Required;

trait ControllerTrait
{
    /** @var SerializerInterface */
    protected SerializerInterface $serializer;

    protected Request $request;

    #[Required]
    public function setRequest(RequestStack $requestStack): void
    {
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @param SerializerInterface $serializer
     */
    #[Required]
    public function setSerializer(SerializerInterface $serializer): void
    {
        $this->serializer = $serializer;
    }

    /**
     * @return array<string, string|array<int, string>>
     */
    public function getSerializerContext(): array
    {
        $context = ['groups' => ['default']];

        if ($expandParam = $this->request->query->get('expand')) {
            $expandParts = explode(',', $expandParam);
            foreach ($expandParts as $expandPart) {
                $groups = explode('.', $expandPart);
                $context['groups'] = [...$context['groups'], ...$groups];
            }
        }

        return $context;
    }

    /**
     * @param mixed $data
     * @param array $serializationContext
     * @return string
     */
    public function getJson(mixed $data, array $serializationContext): string
    {
        if (!$serializationContext) {
            $serializationContext = $this->getSerializerContext();
        }

        return $this->serializer->serialize($data, 'json', array_merge([
            'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
        ], $serializationContext));
    }

    /**
     * @param mixed $data
     * @param array $serializationContext
     * @return JsonResponse
     * @throws \TypeError
     */
    public function okResponse(mixed $data, array $serializationContext = []): JsonResponse
    {
        $json = $this->getJson($data, $serializationContext);

        return new JsonResponse($json, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * @param Paginator $paginator
     * @param array $serializationContext
     * @return JsonResponse
     * @throws \Exception
     */
    public function okCollectionResponse(Paginator $paginator, array $serializationContext = []): JsonResponse
    {
        $data = $paginator->getIterator()->getArrayCopy();

        $json = $this->getJson(
            [
                'data' => $data,
                'meta' => [
                    'page' => (int) $this->request->query->get('page', 1),
                    'count' => count($data),
                    'total' => count($paginator),
                ]
            ],
            $serializationContext
        );

        return new JsonResponse($json, JsonResponse::HTTP_OK, [], true);
    }

    /**
     * @param mixed $data
     * @param array $serializationContext
     * @return JsonResponse
     * @throws \TypeError
     */
    public function createdResponse(mixed $data, array $serializationContext = []): JsonResponse
    {
        $json = $this->getJson($data, $serializationContext);

        return new JsonResponse($json, JsonResponse::HTTP_CREATED, [], true);
    }

    /**
     * @return JsonResponse
     */
    public function noContentResponse(): JsonResponse
    {
        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
