<?php

namespace App\DTO;

use App\DTO\ProductDTO;

use Symfony\Component\Validator\Constraints as Assert;

class CartDTO
{
    #[Assert\Type('countable')]
    #[Assert\NotBlank]
    #[Assert\Type('array')]
    #[Assert\Valid]
    #[Assert\All([
        new Assert\Type(ProductDTO::class),
    ])]
    /** @var array<ProductDTO> */
    public array $cart;
}
