<?php


namespace Tests\Unit;


use HaruhikoZHT\Glicko2\Math\Math;
use HaruhikoZHT\Glicko2\Math\MathInterface;
use HaruhikoZHT\Glicko2\Math\MathOpponent;
use PHPUnit\Framework\TestCase;

class Glicko2MathTest extends TestCase
{
    public static MathInterface $math;

    public static function setUpBeforeClass(): void
    {
        static::$math = new Math(1500);
    }

    public function muProvider(): array
    {
        return [
            ['r' => 1500, 'expected' => 0],
            ['r' => 1400, 'expected' => -0.5756],
            ['r' => 1550, 'expected' => 0.2878],
            ['r' => 1700, 'expected' => 1.1513],
        ];
    }

    /**
     * @test
     * @dataProvider muProvider
     */
    public function mu(float $r, float $expected): void
    {
        $mu = static::$math->mu($r);
        static::assertLessThan(0.1, abs($expected - $mu));
    }

    public function phiProvider(): array
    {
        return [
            ['RD' => 200, 'expected' => 1.1513],
            ['RD' => 30, 'expected' => 0.1727],
            ['RD' => 100, 'expected' => 0.5756],
            ['RD' => 300, 'expected' => 1.7269],
        ];
    }

    /**
     * @test
     * @dataProvider phiProvider
     */
    public function phi(float $RD, float $expected): void
    {
        $phi = static::$math->phi($RD);
        static::assertLessThan(0.0001, abs($expected - $phi));
    }

    public function gProvider(): array
    {
        return [
            ['phi' => 0.1727, 'expected' => 0.9955],
            ['phi' => 0.5756, 'expected' => 0.9531],
            ['phi' => 1.7269, 'expected' => 0.7242],
        ];
    }

    /**
     * @test
     * @dataProvider gProvider
     */
    public function g(float $phi, float $expected): void
    {
        $g = static::$math->g($phi);
        static::assertLessThan(0.0001, abs($expected - $g));
    }

    public function EProvider(): array
    {
        return [
            ['mu' => 0, 'opponent_mu' => -0.5756, 'opponent_phi' => 0.1727, 'expected' => 0.639],
            ['mu' => 0, 'opponent_mu' => 0.2878, 'opponent_phi' => 0.5756, 'expected' => 0.432],
            ['mu' => 0, 'opponent_mu' => 1.1513, 'opponent_phi' => 1.7269, 'expected' => 0.303],
        ];
    }

    /**
     * @test
     * @dataProvider EProvider
     */
    public function E(float $mu, float $opponent_mu, float $opponent_phi, float $expected): void
    {
        $E = static::$math->E($mu, $opponent_mu, $opponent_phi);
        static::assertLessThan(0.001, abs($expected - $E));
    }

    /**
     * @test
     */
    public function v(): void
    {
        static::markTestSkipped();
        /*
        $mathOpponents = [
            new MathOpponent(-0.5756, 0.1727, 1.0),
            new MathOpponent(0.2878, 0.5756, 0.0),
            new MathOpponent(1.1513, 1.7269, 0.0),
        ];
        $v = static::$math->v(0, $mathOpponents);
        static::assertLessThan(0.0001, abs(1.7785 - $v));
        */
    }

    /**
     * @test
     */
    public function delta(): void
    {
        static::markTestSkipped();
        /*
        $mathOpponents = [
            new MathOpponent(-0.5756, 0.1727, 1.0),
            new MathOpponent(0.2878, 0.5756, 0.0),
            new MathOpponent(1.1513, 1.7269, 0.0),
        ];
        $delta = static::$math->delta(0, $mathOpponents);
        static::assertLessThan(0.0001, abs((-0.4843) - $delta));
        */
    }

    /**
     * @test
     */
    public function a(): void
    {
        $a = static::$math->a(0.06);
        static::assertLessThan(0.00001, abs((-5.62682) - $a));
    }

    /**
     * @test
     */
    public function B(): void
    {
        $B = static::$math->B(-0.4834, 1.1513, 1.7785, 0.06, 0.5);
        static::assertLessThan(0.00001, abs((-6.12682) - $B));
    }

    /**
     * @test
     */
    public function new_sigma(): void
    {
        $new_sigma = static::$math->new_sigma(-0.4843, 1.1513, 1.7785, 0.06, 0.5);
        static::assertLessThan(0.00001, abs(0.05999 - $new_sigma));
    }

    /**
     * @test
     */
    public function pre_phi(): void
    {
        $pre_phi = static::$math->pre_phi(1.1513, 0.05999);
        static::assertLessThan(0.00001, abs(1.152862 - $pre_phi));
    }

    /**
     * @test
     */
    public function new_phi(): void
    {
        $new_phi = static::$math->new_phi(1.1513, 0.05999, 1.7785);
        static::assertLessThan(0.0001, abs(0.8722 - $new_phi));
    }

    /**
     * @test
     */
    public function new_mu(): void
    {
        $stubA = $this->createMock(MathOpponent::class);
        $stubA->method('mu')->willReturn(-0.5756);
        $stubA->method('phi')->willReturn(0.1727);
        $stubA->method('s')->willReturn(1.0);

        $stubB = $this->createMock(MathOpponent::class);
        $stubB->method('mu')->willReturn(0.2878);
        $stubB->method('phi')->willReturn(0.5756);
        $stubB->method('s')->willReturn(0.0);

        $stubC = $this->createMock(MathOpponent::class);
        $stubC->method('mu')->willReturn(1.1513);
        $stubC->method('phi')->willReturn(1.7269);
        $stubC->method('s')->willReturn(0.0);

        $mathOpponents = [$stubA, $stubB, $stubC];
        $new_mu = static::$math->new_mu(0, 0.8722, $mathOpponents);
        static::assertLessThan(0.0001, abs((-0.2069) - $new_mu));
    }

    /**
     * @test
     */
    public function new_r(): void
    {
        $new_r = static::$math->new_r(-0.2069);
        static::assertLessThan(0.01, abs(1464.06 - $new_r));
    }

    /**
     * @test
     */
    public function new_RD(): void
    {
        $new_RD = static::$math->new_RD(0.8722);
        static::assertLessThan(0.01, abs(151.52 - $new_RD));
    }
}