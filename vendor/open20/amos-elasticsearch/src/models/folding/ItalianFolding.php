<?php

namespace open20\elasticsearch\models\folding;

class ItalianFolding extends Folding
{
    private $accentFilter = "ìòàùéèioea";
    private $articles     = [
        "c", "l", "all", "dall", "dell",
        "nell", "gl", "un", "m", "t", "s", "v", "d"
    ];

    public function folding($phrase)
    {
        $module = \open20\elasticsearch\Module::instance();
        $phrase = str_replace('"', '', $phrase);
        $parts  = preg_split('/ +/', $phrase);
        $parts  = $this->parseArticles($parts);
        $folded = "";
        foreach ($parts as $key => $part) {
            $char = mb_substr($part, -1);

            if (strpos($this->accentFilter, $char)) {
                $s           = mb_substr($part, 0, -1);
                $parts[$key] = $s."?";
            } else {
                if ($module->useFinalSpecial) {
                    $s           = mb_substr($part, 0);
                    $parts[$key] = $s."?";
                } else {
                    $s           = mb_substr($part, 0);
                    $parts[$key] = $s;
                }
            }
        }
        $folded = implode(" ", $parts);

        return $folded;
    }

    /**
     * 
     * @param array $parts
     */
    protected function parseArticles($parts)
    {
        $purifyArray = [];

        foreach ($parts as $key => $part) {
            $remove = false;
            foreach ($this->articles as $article) {
                $val = str_replace($article, '', $part);
                if (strlen($val) <= 1) {
                    $remove = true;
                    break;
                }
            }
            if (!$remove) {
                $purifyArray[$key] = $part;
            }
        }
        return $purifyArray;
    }
}