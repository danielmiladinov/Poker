<?php
class BestHandIdentifierTest extends PHPUnit_Framework_TestCase
{
    /** @var BestHandIdentifier */
    private $_HandIdentifier;

    /** @var Hand */
    private $_DealtHand;

    /** @var Hand */
    private $_IdentifiedHand;

    public function setUp()
    {
        parent::setUp();
        $this->_HandIdentifier = new BestHandIdentifier();
    }

    public function testWillGetJustAHighCard()
    {
        $this->_theFiveCardsAre('A-S', 'J-C', '7-C', '5-D', '4-S');
        $this->_IdentifiedHand = $this->_HandIdentifier->identify($this->_DealtHand);
        $this->assertInstanceOf('HighCard', $this->_IdentifiedHand);
    }

    public function testWillGetTwoOfAKind()
    {
        $this->_theFiveCardsAre('A-S', 'A-H', 'J-C', '7-C', '5-D');
        $this->_IdentifiedHand = $this->_HandIdentifier->identify($this->_DealtHand);
        $this->assertInstanceOf('TwoOfAKind', $this->_IdentifiedHand);
    }

    public function testWillGetTwoOfAKindWithADifferentPair()
    {
        $this->_theFiveCardsAre('A-S', '7-H', 'J-C', '7-C', '5-D');
        $this->_IdentifiedHand = $this->_HandIdentifier->identify($this->_DealtHand);
        $this->assertInstanceOf('TwoOfAKind', $this->_IdentifiedHand);
    }

    public function testWillGetThreeOfAKind()
    {
        $this->_theFiveCardsAre('7-S', '7-H', '7-C', 'J-C', '5-D');
        $this->_IdentifiedHand = $this->_HandIdentifier->identify($this->_DealtHand);
        $this->assertInstanceOf('ThreeOfAKind', $this->_IdentifiedHand);
    }

    /**
     * @test
     * @param string $card1
     * @param string $card2
     * @param string $card3
     * @param string $card4
     * @param string $card5
     * @dataProvider getSomeCandidatesForAStraight
     */
    public function willGetAStraight($card1, $card2, $card3, $card4, $card5)
    {
        $this->_theFiveCardsAre($card1, $card2, $card3, $card4, $card5);
        $this->_IdentifiedHand = $this->_HandIdentifier->identify($this->_DealtHand);
        $this->assertInstanceOf('Straight', $this->_IdentifiedHand);
    }

    /**
     * @return array
     */
    public function getSomeCandidatesForAStraight()
    {
        return array(
            array( 'A-D',  '2-S',  '3-H',  '4-C',  '5-D',),
            array( '2-S',  '3-H',  '4-C',  '5-D',  '6-S',),
            array( '3-H',  '4-C',  '5-D',  '6-S',  '7-H',),
            array( '4-C',  '5-D',  '6-S',  '7-H',  '8-C',),
            array( '5-D',  '6-S',  '7-H',  '8-C',  '9-D',),
            array( '6-S',  '7-H',  '8-C',  '9-D', '10-S',),
            array( '7-H',  '8-C',  '9-D', '10-S',  'J-H',),
            array( '8-C',  '9-D', '10-S',  'J-H',  'Q-C',),
            array( '9-D', '10-S',  'J-H',  'Q-C',  'K-D',),
            array('10-S',  'J-H',  'Q-C',  'K-D',  'A-S',),
        );
    }

    /**
     * @test
     * @param string $card1
     * @param string $card2
     * @param string $card3
     * @param string $card4
     * @param string $card5
     * @dataProvider getSomeCandidatesForAFlush
     */
    public function willGetAFlush($card1, $card2, $card3, $card4, $card5)
    {
        $this->_theFiveCardsAre($card1, $card2, $card3, $card4, $card5);
        $this->_IdentifiedHand = $this->_HandIdentifier->identify($this->_DealtHand);
        $this->assertInstanceOf('Flush', $this->_IdentifiedHand);
    }

    /**
     * @return array
     */
    public function getSomeCandidatesForAFlush()
    {
        return array(
            array('2-D', '4-D', '6-D', '9-D', '10-D',),
            array('A-C', '3-C', '5-C', 'J-C', 'Q-C',),
            array('3-H', '5-H', '7-H', '8-H', '9-H',),
            array('4-S', '6-S', '8-S', '10-S', 'J-S',),
        );
    }

    public function testWillGetFullHouse()
    {
        $this->_theFiveCardsAre('10-H', '10-C', '10-D', '7-C', '7-D');
        $this->_IdentifiedHand = $this->_HandIdentifier->identify($this->_DealtHand);
        $this->assertInstanceOf('FullHouse', $this->_IdentifiedHand);
    }

    public function testWillGetFourOfAKind()
    {
        $this->_theFiveCardsAre('7-D', '7-S', '7-C', '7-H', 'J-C');
        $this->_IdentifiedHand = $this->_HandIdentifier->identify($this->_DealtHand);
        $this->assertInstanceOf('FourOfAKind', $this->_IdentifiedHand);
    }

    private function _theFiveCardsAre()
    {
        $cards = func_get_args();
        if (count($cards) != 5) {
            $this->fail('lrn2count');
        }

        $this->_DealtHand = new Hand($this->_buildCardStringIntoACardArray($cards));
    }

    private function _buildCardStringIntoACardArray(array $cards)
    {
        $Cards = array();

        foreach ($cards as $cardString) {
            $Cards[] = $this->_makeCardFromString($cardString);
        }

        return $Cards;
    }

    /**
     * @param string $cardString
     * @return Card
     */
    private function _makeCardFromString($cardString)
    {
        $CardBuilder = new CardBuilder();
        return $CardBuilder->fromString($cardString);
    }
}