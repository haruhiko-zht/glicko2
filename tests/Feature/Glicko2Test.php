<?php


namespace Tests\Feature;


use HaruhikoZHT\Glicko2\Calculator;
use HaruhikoZHT\Glicko2\Matches;
use HaruhikoZHT\Glicko2\MatchResult;
use HaruhikoZHT\Glicko2\Player;
use PHPUnit\Framework\TestCase;

class Glicko2Test extends TestCase
{
    protected Matches $matches;
    protected Calculator $calculator;

    protected function setUp(): void
    {
        $this->matches = new Matches();
        $this->calculator = new Calculator();
    }

    public function additionProvider(): array
    {
        return [
            [
                $players = [
                    'foo' => new Player(rd: 200),
                    'bar' => new Player(1400, 30, 0.06),
                    'baz' => new Player(1550, 100, 0.06),
                    'qux' => new Player(1700, 300, 0.06),
                    'quux' => new Player(2000, 100, 0.06)
                ],
                [
                    new MatchResult($players['foo'], 80, $players['bar'], 20),
                    new MatchResult($players['foo'], 60, $players['baz'], 90),
                    new MatchResult($players['foo'], 30, $players['qux'], 100),
                ],
                [
                    'foo' => new Player(1464.06, 151.52, 0.05999),
                    'bar' => new Player(1398.14, 31.67, 0.059999),
                    'baz' => new Player(1570.39, 97.71, 0.059999),
                    'qux' => new Player(1784.42, 251.57, 0.059999),
                    'quux' => new Player(2000, 100.54, 0.06),
                ]
            ]
        ];
    }

    /**
     * @test
     * @dataProvider additionProvider
     *
     * @param Player[] $players
     * @param MatchResult[] $matches
     * @param Player[] $expected
     */
    public function integration(array $players, array $matches, array $expected): void
    {
        $this->matches->addPlayers($players);
        $this->matches->addResults($matches);
        $this->calculator->updateRating($this->matches);

        foreach ($expected as $k => $v) {
            self::assertLessThan(0.1, abs($v->getRating() - $players[$k]->getRating()));
            self::assertLessThan(0.01, abs($v->getRD() - $players[$k]->getRD()));
            self::assertLessThan(0.00001, abs($v->getSigma() - $players[$k]->getSigma()));
        }
    }
}