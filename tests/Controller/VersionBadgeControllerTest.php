<?php

declare(strict_types=1);

/*
 * This file is part of the Symfony project "eliashaeussler/typo3-badges".
 *
 * Copyright (C) 2023 Elias Häußler <elias@haeussler.dev>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

namespace App\Tests\Controller;

use App\Badge\Provider\BadgeProviderFactory;
use App\Controller\VersionBadgeController;
use App\Tests\AbstractApiTestCase;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * VersionBadgeControllerTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class VersionBadgeControllerTest extends AbstractApiTestCase
{
    private VersionBadgeController $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new VersionBadgeController($this->apiService);
        $this->subject->setBadgeProviderFactory(self::getContainer()->get(BadgeProviderFactory::class));
    }

    #[Test]
    public function controllerThrowsBadRequestExceptionIfApiResponseIsInvalid(): void
    {
        $this->mockResponses[] = new MockResponse(json_encode(['foo' => 'baz'], JSON_THROW_ON_ERROR));

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage('Invalid API response.');

        ($this->subject)(new Request(), 'foo');
    }

    #[Test]
    public function controllerReturnsBadgeForGivenExtension(): void
    {
        $this->mockResponses[] = new MockResponse(json_encode([
            [
                'current_version' => [
                    'number' => '1.0.0',
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        $expected = new JsonResponse([
            'schemaVersion' => 1,
            'label' => 'version',
            'message' => '1.0.0',
            'color' => 'orange',
            'isError' => false,
            'namedLogo' => 'TYPO3',
        ]);

        self::assertSame(
            $expected->getContent(),
            ($this->subject)(new Request(), 'foo')->getContent(),
        );
    }
}
