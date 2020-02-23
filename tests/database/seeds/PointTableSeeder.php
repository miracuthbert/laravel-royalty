<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Miracuthbert\Royalty\Models\Point;

class PointTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $points = [
            [
                'name' => 'Subscriber',
                'key' => 'subscriber',
                'points' => 100,
            ],
            [
                'name' => 'Exclusive Seller',
                'key' => 'exclusive-seller',
                'points' => 50,
            ],
            [
                'name' => 'Grades',
                'key' => 'grades',
                'points' => 0,
                'children' => [
                    [
                        'name' => 'Outstanding',
                        'points' => 100,
                    ],
                    [
                        'name' => 'Excellent',
                        'points' => 90,
                    ],
                    [
                        'name' => 'Very Good',
                        'points' => 80,
                    ],
                    [
                        'name' => 'Good',
                        'points' => 70,
                    ],
                ],
            ],
        ];

        foreach ($points as $point) {
            $exists = Point::where('key', $point['key'])->first();

            if (!$exists) {
                Point::create($point);
                return;
            }

            $this->updateOrCreate($point);

            $children = $point['children'] ?? [];

            if (count($children) > 0) {
                foreach ($children as $child) {
                    $this->updateOrCreate($child, $exists);
                }
            }
        }
    }

    /**
     * Create or update a point.
     *
     * @param $point
     * @param null $exists
     */
    protected function updateOrCreate($point, $exists = null)
    {
        $key = $point['key'] ?? Str::slug($exists->name . ' ' . $point['name'], '-');

        Point::updateOrCreate(['key' => $key],
            array_merge(Arr::except($point, ['key', 'children']), [
                'parent_id' => optional($exists)->id
            ])
        );
    }
}
