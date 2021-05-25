<?php


namespace HaruhikoZHT\Glicko2;


/**
 * Class PlayerDetail
 * @package HaruhikoZHT\Glicko2
 */
class PlayerDetail implements PlayerDetailInterface
{
    protected PlayerInterface $player;

    /**
     * @var PlayerResult[]
     */
    protected array $results = [];

    /**
     * PlayerDetail constructor.
     * @param PlayerInterface $player
     */
    public function __construct(PlayerInterface $player)
    {
        $this->player = $player;
    }

    public function getPlayer(): PlayerInterface
    {
        return $this->player;
    }

    public function addResult(PlayerInterface $opponent, float $s): void
    {
        $this->results[] = new PlayerResult($opponent, $s);
    }

    public function getResults(): array
    {
        return $this->results;
    }
}