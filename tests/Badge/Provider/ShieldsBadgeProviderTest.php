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

namespace App\Tests\Badge\Provider;

use App\Badge\Provider\ShieldsBadgeProvider;
use App\Entity\Badge;
use App\Enums\Color;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Route;

/**
 * ShieldsBadgeProviderTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class ShieldsBadgeProviderTest extends KernelTestCase
{
    private ShieldsBadgeProvider $subject;

    protected function setUp(): void
    {
        $this->subject = new ShieldsBadgeProvider(self::getContainer()->get('router'));
    }

    #[Test]
    public function createResponseReturnsResponseForBadge(): void
    {
        $badge = new Badge(
            'foo',
            'baz',
            Color::Orange,
            true,
        );
        $expected = new JsonResponse([
            'schemaVersion' => 1,
            'label' => 'foo',
            'message' => 'baz',
            'color' => 'orange',
            'isError' => true,
            'namedLogo' => 'TYPO3',
        ]);

        self::assertEquals($expected, $this->subject->createResponse($badge));
    }

    #[Test]
    #[DataProvider('generateUriForRouteReturnsUriForGivenRouteDataProvider')]
    public function generateUriForRouteReturnsUriForGivenRoute(Route|string $route): void
    {
        $expected = 'https://shields.io/endpoint?url=https://localhost/badge/foo/downloads/shields';

        self::assertSame($expected, $this->subject->generateUriForRoute($route, ['extension' => 'foo']));
    }

    #[Test]
    public function generateUriForBadgeReturnsUriForGivenBadge(): void
    {
        $badge = new Badge('foo', 'baz', Color::Blue);

        $expected = 'https://img.shields.io/static/v1?label=foo&message=baz&color=blue&logo=typo3';

        self::assertSame($expected, $this->subject->generateUriForBadge($badge));
    }

    /**
     * @return Generator<string, array{Route|string|null}>
     */
    public static function generateUriForRouteReturnsUriForGivenRouteDataProvider(): Generator
    {
        $router = self::getContainer()->get('router');

        yield 'route object' => [$router->getRouteCollection()->get('badge.downloads')];
        yield 'route name' => ['badge.downloads'];
    }
}
