<?php

namespace Miracuthbert\Royalty\Tests\Feature;

use Illuminate\Support\Facades\Event;
use Miracuthbert\Royalty\Events\PointsGiven;
use Miracuthbert\Royalty\Exceptions\PointModelMissingException;
use Miracuthbert\Royalty\Tests\Models\User;
use Miracuthbert\Royalty\Tests\Points\Actions\CompletedTask;
use Miracuthbert\Royalty\Tests\Points\Actions\Subscriber;
use Miracuthbert\Royalty\Tests\TestCase;

class CollectPointsTest extends TestCase
{
    /**
     * Test a user can collect points.
     *
     * @test
     */
    public function user_can_collect_points()
    {
        $user = factory(User::class)->create();

        $subscriber = new Subscriber();

        $this->assertCount(0, $user->pointsRelation()->where('key', $subscriber->key())->get());

        $user->givePoints($subscriber);

        $this->assertCount(1, $user->pointsRelation()->where('key', $subscriber->key())->get());
    }

    /**
     * Test a user has the correct amount of points.
     *
     * @test
     */
    public function user_has_correct_amount_of_points()
    {
        $user = factory(User::class)->create();

        $subscriber = new Subscriber();

        $this->assertCount(0, $user->pointsRelation()->where('key', $subscriber->key())->get());

        $user->givePoints($subscriber);

        $this->assertCount(1, $user->pointsRelation()->where('key', $subscriber->key())->get());

        $this->assertEquals($subscriber->getModel()->points, $user->points()->number());
    }

    /**
     * Test an exception is thrown if point does not exist.
     *
     * @test
     */
    public function exception_thrown_if_point_not_found()
    {
        $user = factory(User::class)->create();

        $completedTask = new CompletedTask();

        $point = $completedTask->getModel();

        $this->assertNull($point);

        $this->assertCount(0, $user->pointsRelation()->where('key', $completedTask->key())->get());

        $this->expectException(PointModelMissingException::class);

        $user->givePoints($completedTask);
    }

    /**
     * Test an event is emitted when a user is given points.
     *
     * @test
     */
    public function an_event_is_emitted_when_user_is_given_points()
    {
        Event::fake();

        $user = factory(User::class)->create();

        $subscriber = new Subscriber();

        $this->assertCount(0, $user->pointsRelation()->where('key', $subscriber->key())->get());

        $user->givePoints($subscriber);

        $this->assertCount(1, $user->pointsRelation()->where('key', $subscriber->key())->get());

        Event::assertDispatched(PointsGiven::class, function ($event) use ($subscriber, $user) {
            return $event->user->id === $user->id && $event->point->id === $subscriber->getModel()->id;
        });
    }
}
