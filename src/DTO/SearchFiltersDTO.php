<?php

namespace App\DTO;
use Symfony\Component\Validator\Constraints as Assert;

class SearchFiltersDTO
{
    public function __construct(
        #[Assert\Range(min: -90, max:90, notInRangeMessage: 'Współrzędna {{ value }} musi być pomiędzy {{ min }} a {{ max }}')]
        public float $lat = 54.1561,
    
        #[Assert\Range(min: -180, max:180, notInRangeMessage: 'Współrzędna {{ value }} musi być pomiędzy {{ min }} a {{ max }}')]
        public float $lng = 19.4045,

        #[Assert\Choice(choices: ['fields', 'forest', 'water'], multiple: true, multipleMessage: 'Jeden lub więcej nieprawidłowych typów obszaru')]
        #[Assert\NotBlank(message: 'Wybierz przynajmniej jeden typ obszaru')]
        public array $area = ['fields'],

        #[Assert\Range(min: 1, max: 9, notInRangeMessage: 'Wartość {{ value }} musi być pomiędzy {{ min }} a {{ max }}')]
        public int $bortle = 4,

        #[Assert\Range(min: 5, max:100, notInRangeMessage: 'Promień nie może być większy niż {{ max }} km i mniejszy niż {{ min }} km')]
        public int $radius = 25,

        #[Assert\PositiveOrZero(message: 'Wysokość musi być liczbą dodatnią lub zerem')]
        public int $elevation = 150

    )
    {}
}