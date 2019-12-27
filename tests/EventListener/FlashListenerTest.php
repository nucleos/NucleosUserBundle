<?php

declare(strict_types=1);

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Tests\EventListener;

use Nucleos\UserBundle\EventListener\FlashListener;
use Nucleos\UserBundle\NucleosUserEvents;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Contracts\Translation\TranslatorInterface;

final class FlashListenerTest extends TestCase
{
    /**
     * @var Event
     */
    private $event;

    /**
     * @var FlashListener
     */
    private $listener;

    protected function setUp(): void
    {
        $this->event = new Event();

        $flashBag = $this->getMockBuilder(FlashBag::class)->getMock();

        $translator = $this->createMock(TranslatorInterface::class);

        $translator->method('trans')
            ->willReturn(static::returnArgument(0))
        ;

        $this->listener = new FlashListener($flashBag, $translator);
    }

    public function testAddSuccessFlash(): void
    {
        $this->expectNotToPerformAssertions();

        $this->listener->addSuccessFlash($this->event, NucleosUserEvents::CHANGE_PASSWORD_COMPLETED);
    }
}
