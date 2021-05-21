<?php


namespace HaruhikoZHT\Glicko2;


class Player implements PlayerInterface
{
    /**
     * @var float player rating
     */
    protected float $rating;

    /**
     * @var float rating deviation (= RD)
     */
    protected float $rd;

    /**
     * @var float volatility (= σ)
     */
    protected float $sigma;

    /**
     * @var PlayerInterface|null previous player instance
     */
    protected ?PlayerInterface $previous;

    /**
     * Player constructor.
     * @param float $rating player rating
     * @param float $rd rating deviation (= RD)
     * @param float $sigma volatility (= σ)
     * @param PlayerInterface|null $previous previous player instance
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

    /**
     * @return float player rating
     */
    public function getRating(): float
    {
        return $this->rating;
    }

    /**
     * @return float rating deviation (= RD)
     */
    public function getRd(): float
    {
        return $this->rd;
    }

    /**
     * @return float volatility (= σ)
     */
    public function getSigma(): float
    {
        return $this->sigma;
    }

    /**
     * @return PlayerInterface|null previous player instance
     */
    public function getPrevious(): ?PlayerInterface
    {
        return $this->previous;
    }
}