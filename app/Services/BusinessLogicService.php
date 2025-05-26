<?php

namespace App\Services;

class BusinessLogicService
{
    public function getIndexData()
    {
        // Return mock data for now
        return [
            'data' => [
                ['id' => 1, 'name' => 'Item 1'],
                ['id' => 2, 'name' => 'Item 2'],
                ['id' => 3, 'name' => 'Item 3'],
            ]
        ];
    }
}
