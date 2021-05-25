<?php


namespace HaruhikoZHT\Glicko2;


/**
 * Class Matches
 * @package HaruhikoZHT\Glicko2
 */
class Matches implements MatchesInterface
{
    /**
     * @var float system constant τ , which constrains the change in volatility over time
     */
    protected float $tau;

    /**
     * @var PlayerInterface[] players
     */
    protected array $players = [];

    /**
     * @var MatchResult[] match results
     */
    protected array $results = [];

    /**
     * Matches constructor.
     * @param float $tau
     *
     * Reasonable choices are between 0.3 and 1.2,
     * though the system should be tested to decide which value results in greatest predictive accuracy.
     * Smaller values of τ prevent the volatility measures from changing by large amounts,
     * which in turn prevent enormous changes in ratings based on very improbable results.
     * If the application of Glicko-2 is expected to involve extremely improbable collections of game outcomes,
     * then  should be set to a small value, even as small as, say, τ = 0.2.
     *
     * This is a quote from "http://glicko.net/glicko.html".
     */
    public function __construct(float $tau = 0.5)
    {
        $this->tau = $tau;
    }

    /**
     * @param MatchResult|MatchResult[] $match_results
     */
    public function addResults(MatchResult|array $match_results): void
    {
        if ($match_results instanceof MatchResult) {
            $this->results[] = $match_results;
        }

        if (is_array($match_results)) {
            $this->results = array_replace(
                $this->results,
                array_filter($match_results, static fn($v) => $v instanceof MatchResult)
            );
        }
    }

    /**
     * @return MatchResult[]
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * @param PlayerInterface[] $players
     */
    public function addPlayers(array $players): void
    {
        $this->players = array_replace($this->players, $players);
    }

    /**
     * @return PlayerInterface[]
     */
    public function getPlayers(): array
    {
        return $this->players;
    }

    /**
     * @return float τ
     */
    public function getTau(): float
    {
        return $this->tau;
    }
}