<?php


namespace HaruhikoZHT\Glicko2;


/**
 * Class Player
 * @package HaruhikoZHT\Glicko2
 */
class Player implements PlayerInterface
{
    /**
     * @var float player rating
     */
    protected float $rating;

    /**
     * @var float RD (= rating deviation)
     */
    protected float $rd;

    /**
     * @var float σ (= volatility)
     */
    protected float $sigma;

    /**
     * @var PlayerInterface|null
     */
    protected ?PlayerInterface $previous;

    /**
     * Player constructor.
     * @param float $rating player rating
     * @param float $rd RD (= rating deviation)
     * @param float $sigma σ (= volatility)
     * @param PlayerInterface|null $previous previous
     */
    public function __construct(
        float $rating = 1500.0,
        float $rd = 350.0,
        float $sigma = 0.06,
        ?PlayerInterface $previous = null
    ) {
        $this->rating = $rating;
        $this->rd = $rd;
        $this->sigma = $sigma;
        $this->previous = $previous;
    }

    public function getRating(): float
    {
        return $this->rating;
    }

    public function getRD(): float
    {
        return $this->rd;
    }

    public function getSigma(): float
    {
        return $this->sigma;
    }

    public function getPrevious(): ?PlayerInterface
    {
        return $this->previous;
    }

    public function update(PlayerInterface $player): PlayerInterface
    {
        $this->previous = new static($this->rating, $this->rd, $this->sigma);
        $this->rating = $player->getRating();
        $this->rd = $player->getRD();
        $this->sigma = $player->getSigma();
        return $this;
    }
}