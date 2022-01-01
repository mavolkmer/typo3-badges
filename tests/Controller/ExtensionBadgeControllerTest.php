<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2021 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace App\Tests\Controller;

use App\Controller\ExtensionBadgeController;
use App\Http\ShieldsEndpointBadgeResponse;
use App\Tests\AbstractApiTestCase;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * ExtensionBadgeControllerTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class ExtensionBadgeControllerTest extends AbstractApiTestCase
{
    private ExtensionBadgeController $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new ExtensionBadgeController($this->apiService);
    }

    /**
     * @test
     */
    public function controllerThrowsBadRequestExceptionIfApiResponseIsInvalid(): void
    {
        $this->mockResponses[] = new MockResponse(json_encode(['foo' => 'baz']));

        $this->expectException(BadRequestHttpException::class);
        $this->expectErrorMessage('Invalid API response.');

        $this->subject->__invoke('foo');
    }

    /**
     * @test
     */
    public function controllerReturnsBadgeForGivenExtension(): void
    {
        $this->mockResponses[] = new MockResponse(json_encode([
            [
                'key' => 'foo',
            ],
        ]));

        $expected = new ShieldsEndpointBadgeResponse([
            'schemaVersion' => 1,
            'label' => 'typo3',
            'message' => 'foo',
            'color' => 'orange',
            'isError' => false,
            'namedLogo' => 'typo3',
        ]);

        self::assertEquals($expected, $this->subject->__invoke('foo'));
    }
}
