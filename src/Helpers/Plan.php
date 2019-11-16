<?php

namespace ImageSeoWP\Helpers;

if (! defined('ABSPATH')) {
    exit;
}

abstract class Plan {
    const ONE_SHOT = [
        [
            'images' => 500,
            'price' => 19.99,
            'name' => 'ImageSEO One Time 500'
        ],
        [
            'images' => 1500,
            'price' => 49.99,
            'name' => 'ImageSEO One Time 1500'
        ],
        [
            'images' => 2500,
            'price' => 79.99,
            'name' => 'ImageSEO One Time 2500'
        ],
        [
            'images' => 5000,
            'price' => 149.99,
            'name' => 'ImageSEO One Time 5000'
        ],
        [
            'images' => 10000,
            'price' => 249.99,
            'name' => 'ImageSEO One Time 10000'
        ]
    ];

    const PLANS = [
        [
            'type' =>  'month',
            'price' =>  0,
            'images' =>  10,
            'name' =>  'Free'
        ],
        [
            'type' => 'month',
            'price' => 4.99,
            'images' => 100,
            'name' => 'Starter'
        ],
        [
            'type' => 'month',
            'price' => 11.99,
            'images' => 250,
            'name' => 'Essential'
        ],
        [
            'type' => 'month',
            'price' => 24.99,
            'images' => 1000,
            'name' => 'Premium'
        ],
        [
            'type' => 'month',
            'price' => 59.99,
            'images' => 2500,
            'name' => 'Enterprise'
        ],
        [
            'type' => 'annual',
            'price' => 49.99,
            'images' => 100,
            'name' => 'Starter'
        ],
        [
            'type' => 'annual',
            'price' => 119.99,
            'images' => 250,
            'name' => 'Essential'
        ],
        [
            'type' => 'annual',
            'price' => 249.99,
            'images' => 1000,
            'name' => 'Premium'
        ],
        [
            'type' => 'annual',
            'price' => 599.99,
            'images' => 4000,
            'name' => 'Enterprise'
        ]
    ];

    /**
     *
     * @param int $numberImages
     * @return null|array
     */
    public static function getOneShotByImages($numberImages){
        $oneShotChoice = null;
        $i = 0;
        $total = count(self::ONE_SHOT);
        while($oneShotChoice === null || $i > $total ){
            if($numberImages > self::ONE_SHOT[$i]['images']){
                $i++;
                continue;
            }

            $oneShotChoice = self::ONE_SHOT[$i];
        }

        return $oneShotChoice;
        
    }
    
    /**
     *
     * @param int $numberImages
     * @return null|array
     */
    public static function getPlanByImages($numberImages){
        $planChoice = null;
        $i = 0;

        $countPlans = count(self::PLANS);
        while($planChoice === null || $i > $countPlans ){
            if($numberImages > self::PLANS[$i]['images']){
                $i++;
                continue;
            }

            $planChoice = self::PLANS[$i];
        }

        return $planChoice;
    }
       
}