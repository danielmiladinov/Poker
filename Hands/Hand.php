<?php

class Hand {
    /**
     * @var Card[]
     */
    private $_Cards;

    /**
     * @param Card[] $Cards
     */
    public function __construct(array $Cards) {
        $this->_Cards = $Cards;
    }

    /**
     * @return Card[]
     */
    public function getCards() {
        return $this->_Cards;
    }

    /**
     * @return Card
     */
    public function getHighCard() {
        $HighCard = null;

        if ($this->isWheel()) {
            $HighCard = array_pop(
                array_filter(
                    array_values($this->_Cards),
                    function (Card $Card) {
                        return $Card->getFaceValue() == Card::FIVE;
                    }
                )
            );
        } else {
            foreach ($this->_Cards as $Card) {
                if (is_null($HighCard) || ($Card->compareFaceValue($HighCard) < 0)) {
                    $HighCard = $Card;
                }
            }
        }

        return $HighCard;
    }

    /**
     * @return Card[]
     */
    public function getCardsGroupedByValues() {
        $CardsGroupedByValues = array();

        foreach ($this->_Cards as $Card) {
            $CardsGroupedByValues[$Card->getFaceValue()][] = $Card;
        }

        return $CardsGroupedByValues;
    }

    /**
     * @param Hand $OtherHand
     * @return bool
     */
    public function equals(Hand $OtherHand) {
        $CardsNotInOtherHand = array_diff($this->getCards(), $OtherHand->getCards());
        $handsAreEqual = count($CardsNotInOtherHand) == 0;
        return $handsAreEqual;
    }

    /**
     * @return boolean
     */
    public function isWheel() {
        return (
            $this->hasCardOfFaceValue(Card::FIVE) &&
            $this->hasCardOfFaceValue(Card::FOUR) &&
            $this->hasCardOfFaceValue(Card::THREE) &&
            $this->hasCardOfFaceValue(Card::TWO) &&
            $this->hasCardOfFaceValue(Card::ACE)
        );
    }

    /**
     * @return string
     */
    public function __toString() {
        $handString = '';

        foreach ($this->getCards() as $Card) {
            $handString .= $Card . '  ';
        }

        $handString .= "({$this->getHandType()})";
        return $handString;
    }

    /**
     * @return string
     */
    protected function getHandType() {
        $handTypeString = '';
        $myClass = get_class($this);

        for ($i = 0; $i < strlen($myClass); $i++) {
            $character = $myClass{$i};
            if (ctype_upper($character)) {
                $handTypeString .= ' ';
            }
            $handTypeString .= $character;
        }

        return trim($handTypeString);
    }

    private function hasCardOfFaceValue($faceValue) {
        foreach ($this->_Cards as $Card) {
            if ($Card->getFaceValue() == $faceValue) {
                return true;
            }
        }
        return false;
    }
}
