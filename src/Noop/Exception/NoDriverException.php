<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\UserBundle\Noop\Exception;

use Exception;
use RuntimeException;

final class NoDriverException extends RuntimeException
{
    public function __construct(?string $message = null, int $code = 0, ?Exception $previous = null)
    {
        parent::__construct(
            null === $message ? 'The child node "db_driver" at path "nucleos_user" must be configured.' : $message,
            $code,
            $previous
        );
    }
}
