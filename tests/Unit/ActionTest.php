<?php

namespace Miracuthbert\Royalty\Tests\Unit;

use Miracuthbert\Royalty\Actions\ActionAbstract;
use Miracuthbert\Royalty\Tests\Points\Actions\Subscriber;
use Miracuthbert\Royalty\Tests\TestCase;

class ActionTest extends TestCase
{
    /**
     * Test an action extends "ActionAbstract".
     *
     * @test
     */
    public function action_is_extends_action_abstract()
    {
        $this->assertTrue(get_parent_class(new Subscriber()) == ActionAbstract::class);
    }
    /**
     * Test an action has a "key" method.
     *
     * @test
     */
    public function action_has_key()
    {
        $action = new Subscriber();

        $this->assertTrue(method_exists($action, 'key'));
    }
}
