<?php

namespace HongyukejiTests\Unit;

use PHPUnit\Framework\TestCase;
use TorMorten\Hongyukeji\Hooks;
use TorMorten\Hongyukeji\HashedCallable;

class HashedCallableTest extends TestCase
{
    public function setUp(): void
    {
        $this->hooks = new Hooks();
    }

    /**
     * @test
     */
    public function it_can_compare_two_callbacks()
    {
        $callback = function () {
            echo 'Action Fired, Baby!';
        };

        $this->hooks->addAction('my_awesome_action', $callback);

        $hashedCallable = new HashedCallable($callback);

        $this->assertTrue($this->hooks->getAction()->getListeners()->first()['callback']->is($hashedCallable));
    }

    /** @test * */
    public function it_can_remove_a_callback()
    {
        $callback = function () {
            echo 'Foo Bar';
        };

        $callback2 = function () {
            echo 'Foo Bars';
        };

        $this->hooks->addAction('my_great_action', $callback);
        $this->assertEquals($this->hooks->getAction()->getListeners()->count(), 1);

        $this->hooks->removeAction('my_great_action', $callback2);
        $this->assertEquals($this->hooks->getAction()->getListeners()->count(), 0);
    }
}
