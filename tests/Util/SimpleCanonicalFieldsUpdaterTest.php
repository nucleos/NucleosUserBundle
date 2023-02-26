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

namespace Nucleos\UserBundle\Tests\Util;

use Nucleos\UserBundle\Tests\App\Entity\TestUser;
use Nucleos\UserBundle\Util\Canonicalizer;
use Nucleos\UserBundle\Util\SimpleCanonicalFieldsUpdater;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class SimpleCanonicalFieldsUpdaterTest extends TestCase
{
    private SimpleCanonicalFieldsUpdater $fieldsUpdater;

    /**
     * @var Canonicalizer&MockObject
     */
    private $usernameCanonicalizer;

    /**
     * @var Canonicalizer&MockObject
     */
    private $emailCanonicalizer;

    protected function setUp(): void
    {
        $this->usernameCanonicalizer = $this->getMockCanonicalizer();
        $this->emailCanonicalizer    = $this->getMockCanonicalizer();

        $this->fieldsUpdater = new SimpleCanonicalFieldsUpdater($this->usernameCanonicalizer, $this->emailCanonicalizer);
    }

    public function testUpdateCanonicalFields(): void
    {
        $user = new TestUser();
        $user->setUsername('Username');
        $user->setEmail('User@Example.com');

        $this->usernameCanonicalizer->expects(static::once())
            ->method('canonicalize')
            ->with('Username')
            ->willReturnCallback('strtolower')
        ;

        $this->emailCanonicalizer->expects(static::once())
            ->method('canonicalize')
            ->with('User@Example.com')
            ->willReturnCallback('strtolower')
        ;

        $this->fieldsUpdater->updateCanonicalFields($user);
        static::assertSame('username', $user->getUsernameCanonical());
        static::assertSame('user@example.com', $user->getEmailCanonical());
    }

    /**
     * @return Canonicalizer&MockObject
     */
    private function getMockCanonicalizer(): MockObject
    {
        return $this->getMockBuilder(Canonicalizer::class)->getMock();
    }
}
