<?php

namespace App\ValueResolver;

use App\Dto\Input\DTOInputInterface;
use App\Service\ValidatorService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ConstructorExtractor;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class InputValueResolver implements ValueResolverInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorService $validatorService
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): array
    {
        $argumentType = $argument->getType();

        if (!$argumentType || !is_subclass_of($argumentType, DTOInputInterface::class)) {
            return [];
        }

        $phpDocExtractor = new PhpDocExtractor();
        $typeExtractor   = new PropertyInfoExtractor(
            typeExtractors: [ new ConstructorExtractor([$phpDocExtractor]), $phpDocExtractor,]
        );

        $serializer = new Serializer(
            normalizers: [
                            new ObjectNormalizer(propertyTypeExtractor: $typeExtractor),
                            new ArrayDenormalizer(),
                        ],
            encoders:    ['json' => new JsonEncoder()]
        );

        $serializer = $this->serializer;
                
        $DTO = [
            $serializer->denormalize(
                json_decode($request->getContent(), true),
                $argument->getType()
            )
        ];

        $this->validatorService->validate($DTO);

        return $DTO;
    }
}