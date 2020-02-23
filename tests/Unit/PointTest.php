<?php

namespace Miracuthbert\Royalty\Tests\Unit;

use Miracuthbert\Royalty\Models\Point;
use Miracuthbert\Royalty\Tests\Points\Actions\Subscriber;
use Miracuthbert\Royalty\Tests\TestCase;

class PointTest extends TestCase
{
    /**
     * @test
     */
    public function a_point_has_correct_key()
    {
        $key = 'red';

        $point = factory(Point::class)->create(['name' => 'Red', 'key' => $key]);

        $this->assertEquals($key, $point->key);
    }

    /**
     * Test a point has correct points.
     *
     * @test
     */
    public function a_point_has_correct_points()
    {
        $points = 100;

        $point = factory(Point::class)->create(['name' => 'Green', 'key' => 'green', 'points' => $points]);

        $this->assertEquals($points, $point->points);
    }

    /**
     * Test a point has a name.
     *
     * @test
     */
    public function a_point_has_correct_name()
    {
        $name = 'Yellow';

        $point = factory(Point::class)->create(['name' => $name, 'key' => 'yellow']);

        $this->assertEquals($name, $point->name);
    }

    /**
     * Test an action point exists.
     *
     * @test
     */
    public function action_has_point_match_in_db()
    {
        $point = new Subscriber();

        $this->assertDatabaseHas('points', $point->getModel()->toArray());
    }
}
