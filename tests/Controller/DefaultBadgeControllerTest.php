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

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * DefaultBadgeControllerTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class DefaultBadgeControllerTest extends WebTestCase
{
    /**
     * @param array<string, string|int|bool> $expected
     */
    #[Test]
    #[DataProvider('controllerReturnsTypo3BadgeDataProvider')]
    public function controllerReturnsTypo3Badge(string $path, array $expected): void
    {
        $client = self::createClient();
        $client->request('GET', $path);
        $json = $client->getResponse()->getContent();

        if (false === $json) {
            throw new RuntimeException('Invalid JSON data.');
        }

        self::assertResponseIsSuccessful();
        self::assertJson($json);
        self::assertSame($expected, json_decode($json, true, 512, JSON_THROW_ON_ERROR));
    }

    /**
     * @return Generator<string, array{string, array<string, string|int|bool>}>
     */
    public static function controllerReturnsTypo3BadgeDataProvider(): Generator
    {
        $badgenResponse = [
            'subject' => 'TYPO3',
            'status' => 'inspiring people to share',
            'color' => 'orange',
        ];
        $shieldsResponse = [
            'schemaVersion' => 1,
            'label' => 'TYPO3',
            'message' => 'inspiring people to share',
            'color' => 'orange',
            'isError' => false,
            'namedLogo' => 'TYPO3',
        ];

        yield 'no explicit provider (fall back to default)' => ['/badge/typo3', $shieldsResponse];
        yield 'badgen' => ['/badge/typo3/badgen', $badgenResponse];
        yield 'shields' => ['/badge/typo3/shields', $shieldsResponse];
    }
}
