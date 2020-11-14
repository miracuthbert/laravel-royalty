<?php

namespace Miracuthbert\Royalty\Tests\Unit;

use Illuminate\Support\Facades\DB;
use Miracuthbert\Royalty\Models\Point;
use Miracuthbert\Royalty\Tests\Models\User;
use Miracuthbert\Royalty\Tests\Points\Actions\DeleteablePoint;
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
     * Test a point can be deleted.
     *
     * @test
     */
    public function a_point_can_be_deleted()
    {
        $user = factory(User::class)->create();

        $point = new DeleteablePoint();

        $pointModel = $point->getModel()->toArray();

        $this->assertDatabaseHas('points', $pointModel);

        $user->givePoints($point);

        $this->assertCount(1, $point->getModel()->users()->get());

        $point->getModel()->delete();

        $this->assertDeleted('points', $pointModel);
        $this->assertCount(0, $user->pointsRelation()->where('key', $point->key())->get());
        $this->assertCount(0, Point::where('key', $point->key())->get());
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
