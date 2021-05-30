<?php


namespace HaruhikoZHT\Glicko2;


use HaruhikoZHT\Glicko2\Math\Math;
use HaruhikoZHT\Glicko2\Math\MathInterface;
use HaruhikoZHT\Glicko2\Math\MathOpponent;

class Calculator
{
    /**
     * @var MatchesInterface
     */
    protected MatchesInterface $matches;

    /**
     * @var MathInterface
     */
    protected MathInterface $math;

    /**
     * Calculator constructor.
     * @param MathInterface|null $math
     */
    public function __construct(?MathInterface $math = null)
    {
        $this->math = $math ?? new Math();
    }

    /**
     * @param MatchesInterface $matches
     * @return PlayerInterface[]
     */
    public function updateRating(MatchesInterface $matches): array
    {
        $this->matches = $matches;

        $player_details = $this->addResultsToPlayerDetails(
            $this->createPlayerDetails($this->matches->getPlayers()),
            $this->matches->getResults()
        );

        $calculated_players = array_map(fn($player_detail) => $this->calcRating($player_detail), $player_details);

        array_map(
            static fn($calculated_player) => $calculated_player->getPrevious()?->update($calculated_player),
            $calculated_players
        );

        return $this->matches->getPlayers();
    }

    /**
     * @param PlayerDetailInterface[] $player_details
     * @param MatchResult[] $results
     * @return PlayerDetailInterface[]
     */
    protected function addResultsToPlayerDetails(array $player_details, array $results): array
    {
        foreach ($results as $result) {
            $player_s = $this->math->s($result->getPlayerScore(), $result->getOpponentScore());
            $opponent_s = $this->math->s($result->getOpponentScore(), $result->getPlayerScore());

            $player_detail = $this->searchPlayerDetail($result->getPlayer(), $player_details);
            if (is_null($player_detail)) {
                $player_detail = new PlayerDetail($result->getPlayer());
            }
            $player_detail->addResult($result->getOpponent(), $player_s);

            $player_detail = $this->searchPlayerDetail($result->getOpponent(), $player_details);
            if (is_null($player_detail)) {
                $player_detail = new PlayerDetail($result->getOpponent());
            }
            $player_detail->addResult($result->getPlayer(), $opponent_s);
        }
        return $player_details;
    }

    /**
     * @param PlayerInterface[] $players
     * @return PlayerDetailInterface[]
     */
    protected function createPlayerDetails(array $players): array
    {
        return array_map(static fn($player) => new PlayerDetail($player), $players);
    }

    /**
     * @param PlayerInterface $player
     * @param PlayerDetailInterface[] $player_details
     * @return PlayerDetailInterface|null
     */
    protected function searchPlayerDetail(PlayerInterface $player, array $player_details): ?PlayerDetailInterface
    {
        $filtered = array_filter(
            $player_details,
            static fn($participant) => $participant->getPlayer() === $player
        );

        return (empty($filtered))
            ? null
            : array_shift($filtered);
    }

    protected function calcRating(PlayerDetailInterface $player_detail): PlayerInterface
    {
        $player = $player_detail->getPlayer();

        if (empty($player_detail->getResults())) {
            $phi = $this->math->phi($player->getRD());
            $new_RD = $this->math->new_RD_without_matching($phi, $player->getSigma());
            return (new Player($player->getRating(), $new_RD, $player->getSigma(), $player));
        }

        /** @var PlayerResult $result */
        $mathOpponents = array_map(
            fn($result) => new MathOpponent(
                $this->math->mu($result->getOpponent()->getRating()),
                $this->math->phi($result->getOpponent()->getRD()),
                $result->getS()
            ),
            $player_detail->getResults()
        );

        // Step 2
        $mu = $this->math->mu($player->getRating());
        $phi = $this->math->phi($player->getRD());

        // Step 3
        $v = $this->math->v($mu, $mathOpponents);

        // Step 4
        $delta = $this->math->delta($mu, $mathOpponents);

        // Step 5
        $new_sigma = $this->math->new_sigma($delta, $phi, $v, $player->getSigma(), $this->matches->getTau());

        // Step 6
        $pre_phi = $this->math->pre_phi($phi, $new_sigma);

        // Step 7
        $new_phi = $this->math->new_phi($phi, $new_sigma, $v);
        $new_mu = $this->math->new_mu($mu, $new_phi, $mathOpponents);

        // Step 8
        $new_r = $this->math->new_r($new_mu);
        $new_RD = $this->math->new_RD($new_phi);

        return (new Player($new_r, $new_RD, $new_sigma, $player));
    }
}