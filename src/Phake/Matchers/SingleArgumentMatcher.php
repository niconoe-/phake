<?php
/*
 * Phake - Mocking Framework
 *
 * Copyright (c) 2010-2022, Mike Lively <m@digitalsandwich.com>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *  *  Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *  *  Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *  *  Neither the name of Mike Lively nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   Testing
 * @package    Phake
 * @author     Mike Lively <m@digitalsandwich.com>
 * @copyright  2010 Mike Lively <m@digitalsandwich.com>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.digitalsandwich.com/
 */

declare(strict_types=1);

namespace Phake\Matchers;

/**
 * Implements matches so that you can easily match a single argument
 */
abstract class SingleArgumentMatcher extends AbstractChainableArgumentMatcher
{
    /**
     * Executes the matcher on a given list of argument values. Returns TRUE on a match, FALSE otherwise.
     *
     * @throws \Phake\Exception\MethodMatcherException
     */
    public function doArgumentsMatch(array &$arguments): void
    {
        $argumentCopy = $arguments;
        $nextArgument = null;

        $nextArgument =& $arguments[0];
        array_shift($argumentCopy);

        $this->matches($nextArgument);

        $nextMatcher = $this->getNextMatcher();
        if (!isset($nextMatcher)) {
            if (0 != count($argumentCopy)) {
                throw new \Phake\Exception\MethodMatcherException('There were more arguments than matchers');
            }
        } else {
            try {
                $nextMatcher->doArgumentsMatch($argumentCopy);
            } catch (\Phake\Exception\MethodMatcherException $e) {
                $e->incrementArgumentPosition();
                throw $e;
            }
        }
    }

    /**
     * Asserts the matcher on a given argument value. Throws an exception on false
     */
    abstract protected function matches(mixed &$argument): void;
}
