<?php
/**
 * PHP Implementation of the Porter Stemmer algorithm for Cms made simple.
 * Russian language.
 *
 * See http://snowball.tartarus.org/algorithms/english/stemmer.html .
 */

class PorterStemmer
{
    private $VOWEL = '/аеиоуыэюя/u';
    private $PERFECTIVEGROUND = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$/u';
    private $REFLEXIVE = '/(с[яь])$/u';
    private $ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|еых|ую|юю|ая|яя|ою|ею)$/u';
    private $PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$/u';
    private $VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/u';
    private $NOUN = '/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|й|и|ы|ь|ию|ью|ю|ия|ья|я)$/u';
    private $RVRE = '/^(.*?[аеиоуыэюя])(.*)$/u';
    private $DERIVATIONAL = '/[^аеиоуыэюя][аеиоуыэюя]+[^аеиоуыэюя]+[аеиоуыэюя].*(?<=о)сть?$/u';

    /**
     * Stems a word.
     *
     * @param  string $word Word to stem
     * @return string       Stemmed word
     */
    public function Stem($word)
    {
        $word = mb_strtolower($word, 'utf-8');
        $word = str_ireplace('ё', 'е', $word);

        $stem = $word;
        do {
            if (!preg_match($this->RVRE, $word, $p)) {
                break;
            }

            $start = $p[1];
            $RV = $p[2];
            if (!$RV) {
                break;
            }

            // Step 1
            if (!$this->s($RV, $this->PERFECTIVEGROUND, '')) {
                $this->s($RV, $this->REFLEXIVE, '');

                if ($this->s($RV, $this->ADJECTIVE, '')) {
                    $this->s($RV, $this->PARTICIPLE, '');
                } else {
                    if (!$this->s($RV, $this->VERB, '')) {
                        $this->s($RV, $this->NOUN, '');
                    }

                }
            }

            // Step 2
            $this->s($RV, '/и$/', '');

            // Step 3
            if ($this->m($RV, $this->DERIVATIONAL)) {
                $this->s($RV, '/ость?$/', '');
            }

            // Step 4
            if (!$this->s($RV, '/ь$/', '')) {
                $this->s($RV, '/ейше?/', '');
                $this->s($RV, '/нн$/', 'н');
            }

            $stem = $start . $RV;
        } while (false);

        return $stem;
    }

    private function s(&$s, $re, $to)
    {
        $orig = $s;
        $s = preg_replace($re, $to, $s);
        return $orig !== $s;
    }

    private function m($s, $re)
    {
        return preg_match($re, $s);
    }
}
