<?php

namespace App\ValueResolver;

use App\Dto\BodyContentArrayDto;
use App\Dto\BodyContentDto;
use App\Dto\Dto;
use App\Dto\MultipartDto;
use App\Dto\QueryDto;
use App\Helper\ValidatorTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DtoResolver implements ValueResolverInterface
{
    use ValidatorTrait;

    public function __construct(
        private SerializerInterface|DenormalizerInterface $serializer,
        ValidatorInterface $validator
    ) {
        $this->setValidator($validator);
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return array<mixed>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): array
    {
        /** @var class-string<Dto> $dtoClassname */
        $dtoClassname = $argument->getType();

        if (!$dtoClassname || !is_subclass_of($dtoClassname, Dto::class)) {
            return [];
        }

        $dtoInstance = new ($dtoClassname)();

        if (is_subclass_of($dtoClassname, QueryDto::class)) {
            $data = $request->query->all();

            /** @var DenormalizerInterface $denormalize */
            $denormalize = $this->serializer;

            $object = $denormalize->denormalize(
                $data,
                $dtoClassname
            );
        } elseif (is_subclass_of($dtoClassname, BodyContentDto::class)) {
            $data = $request->getContent() ?: '{}';

            if ($dtoInstance instanceof BodyContentArrayDto) {
                $type = $dtoInstance->getClassname() . '[]';
            } else {
                $type = $dtoClassname;
            }

            /** @var SerializerInterface $serializer */
            $serializer = $this->serializer;

            $object = $serializer->deserialize(
                $data,
                $type,
                JsonEncoder::FORMAT
            );
        } elseif (is_subclass_of($dtoClassname, MultipartDto::class)) {
            $data = $request->request->all() + $request->files->all();

            /** @var DenormalizerInterface $denormalize */
            $denormalize = $this->serializer;

            $object = $denormalize->denormalize(
                $data,
                $dtoClassname
            );
        } else {
            throw new \LogicException(
                $dtoClassname .
                ' must implement one of the following class QueryDto, BodyContentDto, MultipartDto'
            );
        }


        $this->validateOrFail($object);

        if ($dtoInstance instanceof BodyContentArrayDto) {
            /** @var array<int, BodyContentDto> $object */
            $dtoInstance->setList($object);
            return [$dtoInstance];
        }

        return [$object];
    }
}
