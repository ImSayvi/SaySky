<?php

class Validator
{
    private array $errors = [];
    private array $filters = 
    [
        'radius' => [
            'filter' => FILTER_VALIDATE_INT,
            'options' => [
                'min_range' => 5,
                'max_range' => 150
            ]
        ],
        'lat' => [
            'filter' => FILTER_VALIDATE_FLOAT,
            'options' => [
                'min_range' => -90,
                'max_range' => 90
            ]
        ],
        'lng' => [
            'filter' => FILTER_VALIDATE_FLOAT,
            'options' => [
                'min_range' => -180,
                'max_range' => 180
            ]
        ],
        'bortle' => [
            'filter' => FILTER_VALIDATE_INT,
            'options' => [
                'min_range' => 1,
                'max_range' => 9
            ]
        ],
        'elevation' => [
            'filter' => FILTER_VALIDATE_INT,
            'options' => [
                'min_range' => 0,
                'max_range' => 1500
            ]
        ]
    ];

    public function allowedFilter(string $filterName, $value): bool
    {

        if (!array_key_exists($filterName, $this->filters)) {
            $this->errors[] = "Nieznany filtr: $filterName";
            return false;
        }

        $filter = $this->filters[$filterName];
        return filter_var($value, $filter['filter'], $filter['options']) !== false;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function clearErrors(): void
    {
        $this->errors = [];
    }

    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    public function getFilterKeys(): array
    {
        return array_keys($this->filters);
    }

    public function checkGetForFilters(array $get): array
    {
        $validFilters = [];
        foreach ($this->getFilterKeys() as $filterName) {
            if (isset($get[$filterName]) && $this->allowedFilter($filterName, $get[$filterName])) {
                $validFilters[$filterName] = $get[$filterName];
            }else {
                $this->errors[] = "Nieprawidłowa wartość dla filtra: $filterName";
            }
        }
        return $validFilters;
    }

}