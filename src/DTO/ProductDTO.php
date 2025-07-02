<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class ProductDTO
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Assert\Type('integer')]
    public int $product_id;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Assert\Type('integer')]
    public int $quantity;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Assert\Type('float')]
    public float $unit_price;
}
