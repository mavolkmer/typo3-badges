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

namespace App\Tests\Entity;

use App\Entity\Badge;
use App\Enums\Color;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * BadgeTest.
 *
 * @author Elias Häußler <elias@haeussler.dev>
 * @license GPL-3.0-or-later
 */
final class BadgeTest extends TestCase
{
    #[Test]
    public function forExtensionReturnsBadgeForExtension(): void
    {
        $expected = new Badge(
            'extension',
            'foo',
            Color::Orange,
            false,
        );

        self::assertEquals($expected, Badge::forExtension('foo'));
    }

    #[Test]
    public function forVersionReturnsBadgeForVersion(): void
    {
        $expected = new Badge(
            'version',
            '1.0.0',
            Color::Orange,
            false,
        );

        self::assertEquals($expected, Badge::forVersion('1.0.0'));
    }

    #[Test]
    public function forDownloadsReturnsBadgeForDownloads(): void
    {
        $expected = new Badge(
            'downloads',
            '845.8m',
            Color::Blue,
            false,
        );

        self::assertEquals($expected, Badge::forDownloads(845760473));
    }

    #[Test]
    public function forTypo3VersionsReturnsErrorBadgeForEmptyTypo3VersionList(): void
    {
        $expected = Badge::forError();

        self::assertEquals($expected, Badge::forTypo3Versions([]));
    }

    /**
     * @param list<positive-int> $typo3Versions
     * @param non-empty-string   $expected
     */
    #[Test]
    #[DataProvider('forTypo3VersionsReturnsBadgeForTypo3VersionsDataProvider')]
    public function forTypo3VersionsReturnsBadgeForTypo3Versions(array $typo3Versions, string $expected): void
    {
        $expected = new Badge(
            'TYPO3',
            $expected,
            Color::Orange,
            false,
        );

        self::assertEquals($expected, Badge::forTypo3Versions($typo3Versions));
    }

    /**
     * @param non-empty-string $stability
     */
    #[Test]
    #[DataProvider('forStabilityReturnsBadgeForStabilityDataProvider')]
    public function forStabilityReturnsBadgeForStability(string $stability, Color $expectedColor): void
    {
        $expected = new Badge(
            'stability',
            $stability,
            $expectedColor,
            false,
        );

        self::assertEquals($expected, Badge::forStability($stability));
    }

    #[Test]
    public function forErrorReturnsBadgeOnError(): void
    {
        $expected = new Badge(
            'TYPO3',
            'error',
            Color::Red,
            true,
        );

        self::assertEquals($expected, Badge::forError());
    }

    #[Test]
    public function getLabelReturnsLabel(): void
    {
        $subject = new Badge(
            'foo',
            'baz',
            Color::Orange,
            false,
        );

        self::assertSame('foo', $subject->getLabel());
    }

    #[Test]
    public function getMessageReturnsMessage(): void
    {
        $subject = new Badge(
            'foo',
            'baz',
            Color::Orange,
            false,
        );

        self::assertSame('baz', $subject->getMessage());
    }

    #[Test]
    public function getColorReturnsColor(): void
    {
        $subject = new Badge(
            'foo',
            'baz',
            Color::Orange,
            false,
        );

        self::assertSame(Color::Orange, $subject->getColor());
    }

    #[Test]
    public function isErrorReturnsErrorState(): void
    {
        $subject = new Badge(
            'foo',
            'baz',
            Color::Orange,
            true,
        );

        self::assertTrue($subject->isError());
    }

    /**
     * @return Generator<string, array{non-empty-string, Color}>
     */
    public static function forStabilityReturnsBadgeForStabilityDataProvider(): Generator
    {
        yield 'stable' => ['stable', Color::Green];
        yield 'beta' => ['beta', Color::Yellow];
        yield 'alpha' => ['alpha', Color::Red];
        yield 'experimental' => ['experimental', Color::Red];
        yield 'test' => ['test', Color::Gray];
        yield 'obsolete' => ['obsolete', Color::Gray];
        yield 'excludeFromUpdates' => ['excludeFromUpdates', Color::Gray];
    }

    /**
     * @return Generator<string, array{list<positive-int>, non-empty-string}>
     */
    public static function forTypo3VersionsReturnsBadgeForTypo3VersionsDataProvider(): Generator
    {
        yield 'one version' => [[12], '12'];
        yield 'two versions' => [[11, 12], '11 & 12'];
        yield 'three versions' => [[10, 11, 12], '10, 11 & 12'];
    }
}
