<?php
class Turn
{
    private int $guessIceHoles = 0;
    private int $guessPolarBears = 0;
    private int $guessPenguins = 0;

    public function __construct($iceHoles, $polarBears, $penguins)
    {
        $this->guessIceHoles = $iceHoles;
        $this->guessPolarBears = $polarBears;
        $this->guessPenguins = $penguins;
        $_SESSION['turn']++;
    }

    /**
     * @return int
     */
    public function getGuessIceHoles(): int
    {
        return $this->guessIceHoles;
    }

    /**
     * @return int
     */
    public function getGuessPolarBears(): int
    {
        return $this->guessPolarBears;
    }

    /**
     * @return int
     */
    public function getGuessPenguins(): int
    {
        return $this->guessPenguins;
    }

    // controleer het ingevulde resultaat
    public function check()
    {
        // bereken resultaat
        if($this->resultIceHoles === $iceHoles && $this->resultPolarBears === $polarBears && $this->resultPenguins === $penguins)
        {
            //goed
            $this->score++;
            return 'Goed geraden <br>';
        }else{
            // fout
            $this->wrong++;
            if($this->wrong % 3 == 0)
            {
                // 3x fout geraden, dus random hint laten zien.
                return 'Helaas is je antwoord niet goed. Je krijgt er nu een hint bij: <br>'.$this->hintList->getRandomHint().'<br>';
            }
            return 'Helaas is je antwoord niet goed. <br>';
        }
    }
}