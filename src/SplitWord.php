<?php
namespace usualtool\Search;
use TeamTNT\TNTSearch\Tokenizer\TokenizerInterface;
class SplitWord implements TokenizerInterface{
    protected $mode;
    const MODE_MM   = 'mm';
    const MODE_CHAR = 'char';
    public function __construct($mode = self::MODE_CHAR){
        $this->mode = self::MODE_CHAR;
    }
    public function tokenize($text, $stopwords = []){
        $text = $this->cleanText($text);
        $tokens = [];
        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($chars as $char) {
            if (trim($char) !== '' && !in_array($char, $stopwords)) {
                $tokens[] = $char;
            }
        }
        return array_values($tokens);
    }
    protected function cleanText($text){
        $text = strip_tags($text);
        $text = preg_replace('/[^\p{Han}\p{L}\p{N}]/u', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
    public function getPattern(){
        return '/\s+/';
    }
}